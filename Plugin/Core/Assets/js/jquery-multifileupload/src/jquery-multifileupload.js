/**
 * jQuery Multi File Upload
 *
 * Important pointers:
 * -------------------
 *
 * If files are submitted via iframe transport,
 * then the server always has to respond with an http statuscode of 200
 * regardless of any serverside validation errors.
 *
 * If the response code is not 200, even IE 10 will replace the iframe contents with
 * an error message from disk (res://ieframe.dll/http_500.htm) and cause a cross-domain
 * access denied error.
 *
 * http://stackoverflow.com/questions/151362/access-is-denied-error-on-accessing-iframe-document-object/151404
 */
(function($, win, doc, undefined) {

  /**
   * Namespace for feature detection.
   *
   * @type {Object}
   */
  var support = {};

  /**
   * Detect file input support, based on
   * http://viljamis.com/blog/2012/file-upload-support-on-mobile/
   * Handle devices which give false positives for the feature detection (regex)
   * Feature detection for all other devices (input)
   * @type {boolean}
   */
  support.fileInput = !(new RegExp(
    '(Android (1\\.[0156]|2\\.[01]))' +
      '|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)' +
      '|(w(eb)?OSBrowser)|(webOS)' +
      '|(Kindle/(1\\.0|2\\.[05]|3\\.0))'
  ).test(win.navigator.userAgent) || $('<input type="file">').prop('disabled'));

  /**
   * Check for XHR file upload support.
   *
   * @type {boolean}
   */
  support.xhrFileUpload = !!(win.XMLHttpRequestUpload && win.FileReader);

  /**
   * Check if the FileReader API is available
   *
   * @type {boolean}
   */
  support.fileReader = !!win.FileReader;

  /**
   * Safari supports XHR file uploads via the FormData API,
   * but not non-multipart XHR file uploads.
   *
   * @type {boolean}
   */
  support.xhrFormDataFileUpload = !!win.FormData;

  /**
   * BitrateTimer Constructor
   *
   * @constructor
   */
  var BitrateTimer = function() {
    this.timestamp = ((Date.now) ? Date.now() : (new Date()).getTime());
    this.loaded = 0;
    this.bitrate = 0;
  };

  /**
   * Get the bitrate per second.
   *
   * @param {number} now
   * @param {number} loaded
   * @param {number} interval
   * @returns {number}
   */
  BitrateTimer.prototype.getBitrate = function(now, loaded, interval) {
    var timeDiff = now - this.timestamp;
    if (!this.bitrate || !interval || timeDiff > interval) {
      this.bitrate = (loaded - this.loaded) * (1000 / timeDiff) * 8;
      this.loaded = loaded;
      this.timestamp = now;
    }
    return this.bitrate;
  };

  /**
   * MultiFileUpload Constructor
   *
   * @param el
   * @param options
   * @constructor
   */
  var MultiFileUpload = function(el, options) {

    /**
     * jQuery object of el
     *
     * @type {*|HTMLElement}
     */
    this.$el = $(el);

    /**
     * Holds the MultiFileUpload instance options
     *
     * @type {*}
     */
    this.options = $.extend({}, $.fn.multifileupload.defaults, options);

    /**
     * The file input jquery object
     * on which change events are listened on.
     *
     * @type {*}
     */
    this.$input = this.$el.is('input[type=file]') ? this.$el : this.$el.find('input[type=file]').first();

    if (this.options.url === false) {
      throw new Error('options.url is not set.');
    }

    /**
     * Upload all button
     *
     * @type {*}
     */
    this.$uploadAllBtn = this.$el.find(this.options.uploadAllBtn);

    /**
     * Cancel all button
     *
     * @type {*}
     */
    this.$cancelAllBtn = this.$el.find(this.options.cancelAllBtn);

    /**
     * Determines if an upload is currently
     * in progress.
     *
     * @type {boolean}
     */
    this.uploadInProgress = false;

    /**
     * Holds all files that should be uploaded
     * in the current upload process.
     *
     * @type {Array}
     */
    this.uploadQueue = [];

    /**
     * Uploading deferred.
     * This is used to chain sequential uploads (ajax calls).
     *
     * @type {*}
     */
    this.uploading = $.Deferred();

    /**
     * Holds all fileSets as indexed
     * properties.
     *
     * @type {Object}
     */
    this.fileSets = {};

    /**
     * Holds the active jqXHR deferred object.
     *
     * @type {Object|undefined}
     */
    this.jqXHR = undefined;

    /**
     * Global process object.
     *
     * @type {Object}
     */
    this.progress = {};

    /**
     * Holds an instance of the active BitrateTimer.
     *
     * @type {Object|undefined}
     */
    this.bitRateTimer = undefined;

    this.init();
  };

  /**
   * MultiFileUpload prototype
   */
  MultiFileUpload.prototype = (function() {

    /**
     * Holds all primary events.
     *
     * @type {Array}
     * @private
     */
    var _primaryEvents = [];

    var _imgExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

    var _lastLoaded = 0;

    var _fileSetIndex = 0;

    /**
     * Define all primary events and their
     * corresponding event handlers.
     *
     * @private
     */
    function _buildEvents() {
      _primaryEvents = [
        [this.$input, {
          change: $.proxy(_onInputChange, this)
        }]
      ];

      if (this.$uploadAllBtn.length > 0) {
        _primaryEvents.push(
          [this.$uploadAllBtn, {
            click: $.proxy(_onUploadAll, this)
          }]
        );
      }

      if (this.$cancelAllBtn.length > 0) {
        _primaryEvents.push(
          [this.$cancelAllBtn, {
            click: $.proxy(_onCancelAll, this)
          }]
        );
      }
    }

    /**
     * Check if XHR upload can be used.
     *
     * @returns {boolean}
     * @private
     */
    function _isXHRUpload() {
      return !this.options.forceIframeTransport &&
        (
          (!this.options.multipart && support.xhrFileUpload) ||
          support.xhrFormDataFileUpload
        );
    }

    /**
     * onInputChange event handler
     * is triggered when a file or multiple files
     * are selected on the file input element.
     *
     * Triggers the "mfu-add" event for each file on the multifileupload element
     * and checks if data.context is set on the "mfu-add" event handler.
     *
     * Locally stores the file and context in this.files.
     *
     * @param event
     * @private
     */
    function _onInputChange(event) {
      event.preventDefault();
      event.stopPropagation();

      var files, fileSet, i, len;
      var fileSetDefaults = {
        errors: [],
        files: [],
        mfu: this,
        validates: true,
        size: false,
        $context: undefined,
        $uploadBtn: undefined,
        $cancelBtn: undefined,
        $removeBtn: undefined
      };

      if (event.target.files !== undefined) {
        files = event.target.files;
      } else {
        files = [{
          name: $(event.target).val().replace(/^.*\\/, '')
        }];
      }

      _cloneAndReplaceInput.call(this);

      if (_isXHRUpload.call(this)) {
        /**
         * Create one fileSet for each individual file
         * of the current event.
         */
        for (i = 0, len = files.length; i < len; i++) {
          fileSet = $.extend({}, fileSetDefaults, {
            id: 'fs_' + _fileSetIndex++,
            fileInput: $(event.target),
            files: [],
            errors: []
          });

          files[i].errors = [];
          files[i].ext = files[i].name.split('.').pop().toLowerCase();
          files[i].validates = true;
          _validate.call(this, files[i]);

          //noinspection JSValidateTypes
          if (files[i].size !== undefined) {
            fileSet.size = files[i].size;
          } else {
            fileSet.size = undefined;
          }

          fileSet.files.push(files[i]);

          _handleNewFileSet.call(this, fileSet);
        }
      } else {
        /**
         * We have to use iframe transport where
         * each file selection gets submitted as a whole.
         * Therefore we create a single fileSet for all files
         * of the current event.
         */
        fileSet = $.extend({}, fileSetDefaults, {
          id: 'fs_' + _fileSetIndex++,
          fileInput: $(event.target),
          files: [],
          errors: []
        });

        for (i = 0, len = files.length; i < len; i++) {
          var file = files[i];

          file.errors = [];
          file.ext = file.name.split('.').pop().toLowerCase();
          file.validates = true;
          _validate.call(this, file);

          //noinspection JSValidateTypes
          if (fileSet.size !== undefined) {
            //noinspection JSValidateTypes
            if (file.size !== undefined) {
              fileSet.size += file.size;
            } else {
              fileSet.size = undefined;
            }
          }

          fileSet.files.push(file);
        }

        _handleNewFileSet.call(this, fileSet);
      }

      if (!this.uploadInProgress && _getFileSetCount.call(this) > 0) {
        _enableBtn(this.$uploadAllBtn);
      }
    }

    /**
     * Clone and replace the original input element
     * and keep attached events.
     *
     * @private
     */
    function _cloneAndReplaceInput() {
      var clone = this.$input.clone(true);
      clone.wrap('<form>').closest('form').get(0).reset();
      clone.unwrap();
      this.$input.replaceWith(this.$input = clone);
    }

    /**
     * Handle new filesets.
     *
     * 1. validate the fileset
     * 2. trigger "mfu.add" on the plugin element
     * 3. check existence of $context
     * 4. (optional) register click handlers on buttons
     * 5. if the fileset validates -> process corresponding files
     *
     * @param fileSet
     * @private
     */
    function _handleNewFileSet(fileSet) {
      _validateFileSet.call(this, fileSet);
      this.$el.trigger('add.mfu', [fileSet]);

      if (fileSet.$context === undefined) {
        throw new Error('fileSet.$context is not set on "mfu.add" event.');
      }
      if (fileSet.$uploadBtn !== undefined) {
        if (!fileSet.validates) {
          _disableBtn(fileSet.$uploadBtn);
        }
        fileSet.$uploadBtn.on('click', $.proxy(_onUploadSingle, this, fileSet));
      }
      if (fileSet.$cancelBtn !== undefined) {
        _disableBtn(fileSet.$cancelBtn);
        fileSet.$cancelBtn.on('click', $.proxy(_onCancelSingle, this, fileSet));
      }
      if (fileSet.$removeBtn !== undefined) {
        fileSet.$removeBtn.on('click', $.proxy(_onRemoveSingle, this, fileSet));
      }

      if (fileSet.validates) {
        this.fileSets[fileSet.id] = fileSet;

        for (var i = 0, len = fileSet.files.length; i < len; i++) {
          fileSet.files[i].fileSet = fileSet;
          _process.call(this, fileSet.files[i])
            .always(
              $.proxy(_onProcessComplete, this, fileSet.files[i])
            );
        }
      }
    }

    /**
     * Get the number of registered filesets.
     *
     * @returns {number}
     * @private
     */
    function _getFileSetCount() {
      return $.map(this.fileSets, function(n, i) {
        return i;
      }).length;
    }

    /**
     * Validate a single file.
     *
     * @param {Object} file
     * @private
     */
    function _validate(file) {
      // check for valid file extension
      if (($.inArray('*', this.options.extensions) === -1) &&
        ($.inArray(file.ext, this.options.extensions) === -1)
      ) {
        file.validates = false;
        file.errors.push(
          _applyContext(this.options.messages.fileExtNotAllowed, [file.ext])
        );
      }
      // check for valid mime type
      //noinspection JSValidateTypes
      if (file.type !== undefined &&
        ($.inArray('*', this.options.mimeTypes) === -1) &&
        ($.inArray(file.type, this.options.mimeTypes) === -1)
      ) {
        file.validates = false;
        file.errors.push(
          _applyContext(this.options.messages.mimeTypeNotAllowed, [file.type])
        );
      }
      // check for valid minimum filesize
      //noinspection JSValidateTypes
      if (file.size !== undefined) {
        if (file.size < this.options.minFileSize) {
          file.validates = false;
          file.errors.push(
            _applyContext(this.options.messages.fileTooSmall, [
              file.name,
              this.bytesToHuman(this.options.minFileSize)
            ])
          );
        }
        // check for valid maximum filesize
        if (file.size > this.options.maxFileSize) {
          file.validates = false;
          file.errors.push(
            _applyContext(this.options.messages.fileTooBig, [
              file.name,
              this.bytesToHuman(this.options.maxFileSize)
            ])
          );
        }
      }
    }

    /**
     * Validate a complete fileset.
     *
     * @param fs
     * @private
     */
    function _validateFileSet(fs) {
      if (fs.files.length > this.options.maxFilesPerUpload) {
        fs.validates = false;
        fs.errors.push(
          _applyContext(this.options.messages.tooManyFiles, [this.options.maxFilesPerUpload])
        );
      }

      //noinspection JSValidateTypes
      if (fs.size !== undefined && fs.size > this.options.maxUploadSize) {
        fs.validates = false;
        fs.errors.push(
          _applyContext(this.options.messages.uploadSizeExceeded, [
            this.bytesToHuman(this.options.maxUploadSize)
          ])
        );
      }

      if (fs.validates) {
        for (var i = 0, len = fs.files.length; i < len; i++) {
          if (!fs.files[i].validates) {
            fs.validates = false;
            break;
          }
        }
      }
    }

    /**
     * Invoke the next process in the processingQueue
     * of a single file.
     *
     * @param file
     * @private
     */
    function _invokeProcess(file) {
      var dfd = $.Deferred();
      var processFn = file.processingQueue.shift();
      var that = this;

      dfd.promise().then(
        function() {
          if (file.processingQueue.length >= 1) {
            _invokeProcess.call(that, file);
          }
          file.processing.resolve();
        },
        function() {
          if (file.processing !== undefined) {
            file.processing.reject();
          }
        }
      );

      try {
        _processor[processFn].call(this, dfd, file);
      } catch(error) {
        file.processing.reject(error);
      }
    }

    /**
     * Process a single file.
     *
     * @param {Object} file
     * @private
     */
    function _process(file) {
      $.extend(file, {
        processing: $.Deferred(),
        processingQueue: this.options.processingQueue.slice()
      });

      _invokeProcess.call(this, file);

      return file.processing.promise();
    }

    /**
     * File processor
     * Holds methods to process
     * images, audio and video files.
     */
    var _processor = (function() {

      /**
       * Load an image via FileReader API.
       *
       * @param {File} file
       * @param {Function} callback
       * @private
       */
      function _loadImage(file, callback) {
        var reader = new FileReader();
        reader.onload = function(event) {
          var img = new Image();
          img.onload = function() {
            callback({
              width: this.width,
              height: this.height,
              imgObj: img
            });
          };
          img.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }

      /**
       * Load the data url of any file.
       *
       * @param {File} file
       * @param {Function} callback
       * @private
       */
      function _loadFile(file, callback) {
        var reader = new FileReader();
        reader.onload = function(event) {
          callback(event.target.result);
        };
        reader.readAsDataURL(file);
      }

      /**
       * Resize + crop an image and
       * draw the results on the given canvas.
       *
       * @param img
       * @param canvas
       * @private
       */
      function _resizeCropImgOnCanvas(img, canvas) {
        var scale;
        var destWidth = this.options.previewImageWidth;
        var destHeight = this.options.previewImageHeight;
        var scaleSrc = img.width / img.height;
        var scaleDst = destWidth / destHeight;

        if (scaleSrc < scaleDst) {
          scale = img.width / destWidth;
        } else {
          scale = img.height / destHeight;
        }

        var sourceWidth = Math.ceil(img.width / scale);
        var sourceHeight = Math.ceil(img.height / scale);
        var sourceX = Math.ceil((sourceWidth - destWidth) / 2);
        var sourceY = Math.ceil((sourceHeight - destHeight) / 2);

        var $tmpCanvas = $('<canvas/>').attr({
          width: sourceWidth,
          height: sourceHeight
        });
        $tmpCanvas[0].getContext('2d').drawImage(
          img.imgObj, 0, 0, sourceWidth, sourceHeight
        );

        var tmpImage = new Image();
        tmpImage.onload = function() {
          canvas.getContext('2d').drawImage(
            tmpImage, sourceX, sourceY, destWidth, destHeight, 0, 0, destWidth, destHeight
          );
        };
        tmpImage.src = $tmpCanvas[0].toDataURL();
      }

      return {

        image: function(deferred, file) {
          if (!support.fileReader ||
              $.inArray(file.ext, _imgExtensions) === -1 ||
              file.size > this.options.maxPreviewImageSize
          ) {
            return deferred.resolve();
          }
          var that = this;
          _loadImage.call(this, file, function(img) {
            file.img = img;
            file.$canvas = $('<canvas/>').attr({
              width: that.options.previewImageWidth,
              height: that.options.previewImageHeight
            });
            _resizeCropImgOnCanvas.call(that, file.img, file.$canvas[0]);
            return deferred.reject();
          });
          return deferred.promise();
        },

        audio: function(deferred, file) {
          var $audio = $('<audio/>');
          if (!support.fileReader ||
              !$audio[0].canPlayType ||
              !$audio[0].canPlayType(file.type) ||
              file.size > this.options.maxPreviewAudioSize
          ) {
            return deferred.resolve();
          }
          file.$audio = $audio.css('width', this.options.previewImageWidth);
          _loadFile.call(this, file, function(dataUrl) {
            file.$audio[0].src = dataUrl;
            file.$audio[0].controls = true;
            return deferred.reject();
          });
          return deferred.promise();
        },

        video: function(deferred, file) {
          var $video = $('<video/>');
          if (!support.fileReader ||
              !$video[0].canPlayType ||
              !$video[0].canPlayType(file.type) ||
              file.size > this.options.maxPreviewVideoSize
          ) {
            return deferred.resolve();
          }
          var that = this;
          file.$video = $video;
          _loadFile.call(this, file, function(dataUrl) {
            file.$video[0].src = dataUrl;
            file.$video[0].controls = true;
            file.$video[0].width = that.options.previewImageWidth;
            return deferred.reject();
          });
          return deferred.promise();
        }
      }
    })();

    /**
     * onProcessComplete callback
     * Called, when the processing of a single file is completed.
     * Triggers the "mfu.processed" event on the
     * plugin element.
     *
     * @param file
     * @private
     */
    function _onProcessComplete(file) {
      delete file.processing;
      delete file.processingQueue;
      this.$el.trigger('processed.mfu', [file]);
    }

    /** ---------------------------------- INTERFACE HANDLERS ------------------------------------------------------ **/

    /**
     * Gets fired if the upload all button is clicked.
     * Enqueues all filesets to be uploaded and
     * starts the upload process.
     *
     * @param event
     * @private
     */
    function _onUploadAll(event) {
      event.preventDefault();
      event.stopPropagation();

      if (this.$uploadAllBtn.hasClass('disabled')) {
        return;
      }

      _disableBtn(this.$uploadAllBtn);

      for (var i = 0; i < _fileSetIndex; i++) {
        var idx = 'fs_' + i;
        if (this.fileSets.hasOwnProperty(idx)) {
          this.uploadQueue.push(this.fileSets[idx]);
          _disableBtn(
            this.fileSets[idx].$uploadBtn,
            this.fileSets[idx].$removeBtn
          );
          _enableBtn(
            this.fileSets[idx].$cancelBtn
          )
        }
      }

      _enableBtn(this.$cancelAllBtn);
      _upload.call(this);
    }

    /**
     * Gets fired if the cancel all button is clicked.
     * Empties the upload queue and aborts
     * the current ajax request.
     *
     * @param event
     * @private
     */
    function _onCancelAll(event) {
      event.preventDefault();
      event.stopPropagation();

      if (this.$cancelAllBtn.hasClass('disabled')) {
        return;
      }

      _disableBtn(this.$cancelAllBtn);
      this.uploadQueue = [];
      if (this.jqXHR !== undefined) {
        this.jqXHR.abort();
      }
    }

    /**
     * Gets fired if a single fileset's upload button is clicked.
     * Enqueues the fileset and starts the upload.
     *
     * @param fileSet
     * @private
     */
    function _onUploadSingle(fileSet) {
      if (fileSet.$uploadBtn.hasClass('disabled')) {
        return;
      }

      _disableBtn(
        this.$uploadAllBtn,
        fileSet.$uploadBtn,
        fileSet.$removeBtn
      );
      _enableBtn(
        fileSet.$cancelBtn,
        this.$cancelAllBtn
      );
      this.uploadQueue.push(fileSet);
      _upload.call(this);
    }

    /**
     * Gets fired if a single fileset's cancel button is clicked.
     * Aborts the ajax call, but does not empty
     * the upload queue.
     *
     * @param fileSet
     * @private
     */
    function _onCancelSingle(fileSet) {
      if (fileSet.$cancelBtn.hasClass('disabled')) {
        return;
      }

      _disableBtn(
        fileSet.$cancelBtn
      );
      _enableBtn(
        fileSet.$uploadBtn,
        fileSet.$removeBtn
      );

      if (this.jqXHR !== undefined) {
        this.jqXHR.abort();
      }

      this.$el.trigger('cancel-local.mfu', [fileSet]);
    }

    /**
     * Gets fired if a single filesets's remove button is clicked.
     * Removes the fileset.
     *
     * @param fileSet
     * @private
     */
    function _onRemoveSingle(fileSet) {
      if (fileSet.$removeBtn.hasClass('disabled')) {
        return;
      }

      _disableBtn(
        fileSet.$removeBtn,
        fileSet.$cancelBtn,
        fileSet.$uploadBtn
      );
      this.fileSets.hasOwnProperty(fileSet.id) && delete this.fileSets[fileSet.id];
      this.$el.trigger('remove-local.mfu', [fileSet]);
    }

    /** ------------------------------- GLOBAL UPLOAD HANDLERS ----------------------------------------------------- **/

    /**
     * Gets fires once, when the upload process for
     * a fileset, or multiple filesets starts.
     * Sets up the global progress object.
     *
     * @param uploadQueue
     * @private
     */
    function _onUploadsStart(uploadQueue) {
      this.progress = {
        minLoaded: 0,
        bitrate: 0,
        time: 0,
        percent: 0,
        loaded: 0,
        total: _getTotalFileSize.call(this, uploadQueue),
        filesLoaded: 0,
        filesTotal: _getTotalFileCount.call(this, uploadQueue)
      };
      this.$el.trigger('start.mfu', [this]);
      this.$el.trigger('progress.mfu', [this.progress, this]);
    }

    /**
     * Gets fired manually on each xhr.progress and upload complete event.
     *
     * @param {*=} event
     * @private
     */
    function _onProgressGlobal(event) {
      if (event) {
        this.progress.loaded += (event.loaded - _lastLoaded);
        _lastLoaded = event.loaded;
      }
      if (this.progress.total !== undefined) {
        this.progress.percent = Math.ceil(this.progress.loaded / this.progress.total * 100);
      } else {
        this.progress.percent = Math.ceil(this.progress.filesLoaded / this.progress.filesTotal * 100);
      }
      if (this.bitRateTimer !== undefined) {
        this.progress.bitrate = this.bitRateTimer.getBitrate($.now(), _lastLoaded, this.options.bitrateInterval);
      }
      this.$el.trigger('progress.mfu', [this.progress, this]);
    }

    /**
     * Gets fired if an upload queue has been completely
     * processed and all ajax calls of this queue have
     * completed.
     *
     * @private
     */
    function _onUploadsComplete() {
      this.uploadInProgress = false;
      _disableBtn(this.$cancelAllBtn);
      if (_getFileSetCount.call(this) > 0) {
        _enableBtn(this.$uploadAllBtn);
      } else {
        _disableBtn(this.$uploadAllBtn);
      }
      this.$el.trigger('complete.mfu', [this]);
    }

    /** ------------------------------- LOCAL UPLOAD HANDLERS ------------------------------------------------------ **/

    /**
     * Gets fired before each individual ajax call.
     *
     * @param fileSet
     * @private
     */
    function _onUploadStart(fileSet) {
      if (_isXHRUpload.call(this)) {
        this.bitRateTimer = new BitrateTimer();
      }
      //noinspection JSValidateTypes
      if (fileSet.size !== undefined) {
        this.progress.minLoaded += fileSet.size;
      }
      _lastLoaded = 0;
    }

    /**
     * Gets fired manually on each xhr.progress event
     * to update the individual fileset's upload progress.
     *
     * @param event
     * @param fileSet
     * @private
     */
    function _onProgressLocal(event, fileSet) {
      if (event.lengthComputable) {
        var percent = Math.ceil(event.loaded / event.total * 100);
        this.$el.trigger('progress-local.mfu', [percent, fileSet]);
        _onProgressGlobal.call(this, event);
      }
    }

    function _onUploadError(response, fileSet) {
      console.log(response);
      var errors = [];
      if (!response) {
        alert('Something went wrong.');
      } else if (response.status === undefined) {
        alert('Server has to respond with a json response, containing a status key.');
      } else if (response.status === 'error') {
        if (response.errors !== undefined && response.errors.length > 0) {
          for (var i = 0, len = response.errors.length; i < len; i++) {
            var error = response.errors[i];
            errors.push(_applyContext(error.message, error.context));
          }
        }
      }
      _disableBtn(fileSet.$cancelBtn);
      _enableBtn(fileSet.$removeBtn);
      delete this.fileSets[fileSet.id];
      this.$el.trigger('error.mfu', [errors, fileSet]);
    }

    /**
     * Gets fired after the successful completion of
     * each individual fileset's upload.
     *
     * @param response
     * @param fileSet
     * @private
     */
    function _onUploadComplete(response, fileSet) {
      this.progress.loaded = this.progress.minLoaded;
      this.progress.filesLoaded += fileSet.files.length;
      this.fileSets.hasOwnProperty(fileSet.id) && delete this.fileSets[fileSet.id];
      _onProgressGlobal.call(this);
      _disableBtn(fileSet.$cancelBtn, fileSet.$removeBtn);
      this.$el.trigger('complete-local.mfu', [fileSet]);
    }

    /** -------------------------------- UPLOAD FUNCTIONS ---------------------------------------------------------- **/

    /**
     * Sequentially process the current upload queue.
     *
     * @private
     */
    function _upload() {
      if (this.uploadQueue.length === 0) {
        return;
      }

      var that = this;
      var upload = function() {
        if (!that.uploadInProgress) {
          that.uploadInProgress = true;
          _onUploadsStart.call(that, that.uploadQueue);
        }
        return (function loop() {
          var fileSet = that.uploadQueue.shift();
          return fileSet && _uploadFileSet.call(that, fileSet).then(loop);
        })();
      };

      this.uploading
        .then(upload)
        .done($.proxy(_onUploadsComplete, this));

      this.uploading.resolve();
    }

    /**
     * Upload a fileset.
     *
     * @param fileSet
     * @returns {jQuery.ajax}
     * @private
     */
    function _uploadFileSet(fileSet) {
      var that = this;

      var dataType = 'json';
      var isXHRUpload = _isXHRUpload.call(this);

      if (!isXHRUpload) {
        dataType = 'mfuiframe ' + dataType;
      }

      return $.ajax({
        url: this.options.url,
        type: 'post',
        contentType: false,
        processData: false,
        cache: false,
        dataType: dataType,
        fileInput: fileSet.fileInput,
        beforeSend: function(jqXHR, settings) {
          if (isXHRUpload) {
            _setupXHR.call(that, jqXHR, settings, fileSet);
          }
          _onUploadStart.call(that, fileSet);
          that.jqXHR = jqXHR;
        },
        success: function(response) {
          //noinspection JSValidateTypes
          if (!response ||
            response.status === undefined ||
            response.status === 'error'
          ) {
            _onUploadError.call(that, response, fileSet);
          } else {
            _onUploadComplete.call(that, response, fileSet);
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(textStatus);
          console.log(errorThrown);
        },
        complete: function(jqXHR) {
          that.jqXHR = undefined;
        }
      });
    }

    /**
     * Setup the xhr progress event listener
     * and register the jqXHR object in the global scope
     * to enable upload canceling.
     *
     * @param jqXHR
     * @param settings
     * @param fileSet
     * @private
     */
    function _setupXHR(jqXHR, settings, fileSet) {
      var that = this;
      var formData = new FormData();
      var inputName = this.$input.attr('name');
      var paramName = (
        this.options.paramName !== undefined &&
        this.options.paramName !== ''
      ) ? this.options.paramName : (
        inputName !== undefined &&
        inputName !== ''
      ) ? inputName : 'files[]';

      formData.append(paramName, fileSet.files[0]);
      settings.data = formData;
      settings.xhr = function() {
        var xhr = $.ajaxSettings.xhr();
        if (xhr.upload) {
          xhr.upload.addEventListener('progress', function(event) {
            _onProgressLocal.call(that, event, fileSet);
          }, false);
        }
        return xhr;
      };
    }

    /**
     * The the total file size for the
     * specified uploadQueue.
     *
     * @param {Array} uploadQueue
     * @returns {number}
     * @private
     */
    function _getTotalFileSize(uploadQueue) {
      var total = 0;

      for (var i = 0, len = uploadQueue.length; i < len; i++) {
        //noinspection JSValidateTypes
        if (uploadQueue[i].size !== undefined) {
          total += uploadQueue[i].size;
        } else {
          total = undefined;
          break;
        }
      }

      return total;
    }

    /**
     * Get the total number of files for the
     * specified uploadQueue.
     *
     * @param {Array} uploadQueue
     * @returns {number}
     * @private
     */
    function _getTotalFileCount(uploadQueue) {
      var total = 0;

      for (var i = 0, len = uploadQueue.length; i < len; i++) {
        total += uploadQueue[i].files.length;
      }

      return total;
    }

    /**
     * Enable a single or multiple buttons.
     * Accepts jquery objects as arguments.
     *
     * @private
     */
    function _enableBtn() {
      for (var i = 0, len = arguments.length; i < len; i++) {
        if (arguments[i] && arguments[i].length > 0) {
          arguments[i].removeClass('disabled');
        }
      }
    }

    /**
     * Disable a single or multiple buttons.
     * Accepts jquery objects as arguments.
     *
     * @private
     */
    function _disableBtn() {
      for (var i = 0, len = arguments.length; i < len; i++) {
        if (arguments[i] && arguments[i].length > 0) {
          arguments[i].addClass('disabled');
        }
      }
    }

    /**
     * Replace {0} ... {n} array values of context
     * in message.
     *
     * @param {string} message
     * @param {Array=} context
     * @returns {*}
     * @private
     */
    function _applyContext(message, context) {
      if (context) {
        $.each(context, function (key, value) {
          message = message.replace('{' + key + '}', value);
        });
      }
      return message;
    }

    /**
     * Public functions
     */
    return {

      /**
       * Constructor
       */
      constructor: MultiFileUpload,

      /**
       * Initialization fn
       */
      init: function() {
        _buildEvents.call(this);
        $.attachEvents(_primaryEvents);
      },

      /**
       * Creates a human readable string
       * of a given number of bytes and the specified
       * precision.
       *
       * @param {number} bytes
       * @param {number} precision
       * @returns {string}
       */
      bytesToHuman: function(bytes, precision) {
        if (typeof bytes !== 'number') {
          return '';
        }

        var kb = 1024;
        var mb = kb * 1024;
        var gb = mb * 1024;

        if ((bytes >= 0) && (bytes < kb)) {
          return bytes + 'B';
        } else if ((bytes >= kb) && (bytes < mb)) {
          return (bytes / kb).toFixed(precision) + ' KB';
        } else if ((bytes >= mb) && (bytes < gb)) {
          return (bytes / mb).toFixed(precision) + ' MB';
        } else if (bytes >= gb) {
          return (bytes / gb).toFixed(precision) + ' GB';
        } else {
          return bytes + ' B';
        }
      },

      /**
       * Creates a human readable string
       * of a given number of bits and the specified
       * precision.
       *
       * Data transfers use a metric system (1000 instead of 1024).
       *
       * @param {number} bits
       * @param {number} precision
       * @returns {string}
       */
      bitRateToHuman: function(bits, precision) {
        if (typeof bits !== 'number') {
          return '';
        }

        var kb = 1000;
        var mb = kb * 1000;
        var gb = mb * 1000;

        if ((bits >= 0) && (bits < kb)) {
          return bits + 'bit/s';
        } else if ((bits >= kb) && (bits < mb)) {
          return (bits / kb).toFixed(precision) + ' Kbit/s';
        } else if ((bits >= mb) && (bits < gb)) {
          return (bits / mb).toFixed(precision) + ' Mbit/s';
        } else if (bits >= gb) {
          return (bits / gb).toFixed(precision) + ' Gbit/s';
        } else {
          return bits + ' bit/s';
        }
      }

    }

  })();

  var iframeCount = 0;
  $.ajaxTransport('mfuiframe', function(options) {
    if (!options.async) {
      return {};
    }

    var $iframe, $form, paramSep, cb;

    function _onInitialLoad() {
      $iframe
        .unbind('load')
        .bind('load', _onRequestLoad);

      $form
        .prop('target', $iframe.prop('name'))
        .prop('action', options.url)
        .prop('method', options.type);

      if (options.fileInput !== undefined && options.fileInput.length > 0) {
        $form
          .append(options.fileInput)
          .prop('enctype', 'multipart/form-data')
          .prop('encoding', 'multipart/form-data');
      }

      $form.submit();
    }

    function _onRequestLoad() {
      var response;

      /**
       * Wrap in a try/catch block to catch exceptions thrown
       * when trying to access cross-domain iframe contents.
       */
      try {
        response = $iframe.contents();

        /**
         * Google Chrome and Firefox do not throw an
         * exception when calling iframe.contents() on
         * cross-domain requests, so we unify the response.
         */
        if (!response.length || !response[0].firstChild) {
          //noinspection ExceptionCaughtLocallyJS
          throw new Error();
        }
      } catch(e) {
        response = undefined;
      }

      /**
       * The complete callback returns the
       * iframe content document as response object.
       */
      cb(200, 'success', {mfuiframe: response});

      /**
       * Fix for IE endless progress bar activity bug
       * (happens on form submits to iframe targets)
       */
      $('<iframe src="javascript:false"/>').appendTo($form);

      /**
       * Removing the form in a setTimeout call
       * allows Chrome's dev tools to display
       * the response result.
       */
      setTimeout(function() {
        $iframe.remove();
        $form.remove();
      }, 0);
    }

    /**
     * Send fn
     *
     * @param headers
     * @param completeCallback
     * @private
     */
    function _send(headers, completeCallback) {
      $form = $('<form style="display: none;"/>');
      $form.attr('accept-charset', options.formAcceptCharset);
      paramSep = /\?/.test(options.url) ? '&' : '?';
      options.url = options.url + paramSep + '_method=PUT';
      cb = completeCallback;

      iframeCount++;
      $iframe = $('<iframe src="javascript:false;" name="mfu-iframe-transport-' + iframeCount + '"/>');
      $iframe.bind('load', _onInitialLoad);
      $form.appendTo(doc.body);
      $iframe.appendTo(doc.body);
    }

    /**
     * Abort fn
     *
     * @private
     */
    function _abort() {
      if ($iframe.length > 0) {
        $iframe.unbind('load').prop('src', 'javascript:false;');
        $iframe.remove();
      }
      if ($form.length > 0) {
        $form.remove();
      }
    }

    return {
      send: _send,
      abort: _abort
    };

  });

  /**
   * Setup Ajax converter
   */
  $.ajaxSetup({
    converters: {
      'mfuiframe json': function (mfuiframe) {
        return mfuiframe && $.parseJSON($(mfuiframe[0].body).text());
      }
    }
  });

  $.fn.multifileupload = function(options) {
    if (!options || typeof options === 'object') {
      return this.each(function() {
        if (!$(this).data('multifileupload')) {
          $(this).data('multifileupload', new MultiFileUpload(this, options));
        }
      });
    } else if (typeof options === 'string' && options.charAt(0) !== '_') {
      var multiFileUpload = this.data('multifileupload');
      if (!multiFileUpload) {
        throw new Error('multifileupload is not initialized on this DOM element.');
      }
      if (multiFileUpload && multiFileUpload[options]) {
        return multiFileUpload[options].apply(multiFileUpload, Array.prototype.slice.apply(arguments, [1]))
      }
    }
    throw new Error('"' + options + '" is no valid api method.');
  };

  $.fn.multifileupload.defaults = {
    /**
     * Upload endpoint
     */
    url: false,
    /**
     * The drop target element(s), by the default the complete document.
     * Set to null to disable drag & drop support.
     */
    dropZone: $(doc),
    /**
     * The paste target element(s), by the default the complete document.
     * Set to null to disable paste support.
     */
    pasteZone: $(doc),
    /**
     * The parameter name for the file form data (the request argument name).
     * If undefined or empty, the name property of the file input field is
     * used, or "files[]" if the file input name property is also empty,
     * can be a string or an array of strings.
     */
    paramName: undefined,
    /**
     * Set this option to true to force iframe transport uploads.
     */
    forceIframeTransport: false,
    /**
     * Interval in milliseconds to calculate progress bitrate.
     */
    bitrateInterval: 500,
    /**
     * By default, uploads are not started automatically when adding files.
     */
    autoUpload: false,
    /**
     * By default, allow all file extensions.
     */
    extensions: ['*'],
    /**
     * By default, allow all mime types.
     */
    mimeTypes: ['*'],
    /**
     * The minimum allowed file size in bytes (1 Byte default).
     */
    minFileSize: 1,
    /**
     * The maximum allowed file size in bytes (5 MB default).
     */
    maxFileSize: 5242880,
    /**
     * The maxmium number of files per upload request (20 default).
     */
    maxFilesPerUpload: 20,
    /**
     * The maximum allowed upload size in bytes (100 MB default).
     * If iframe transport is used, then multiple files may be submitted
     * with one request.
     *
     * e.g. with default values:
     * 100 MB (maxUploadSize) = 20 files (maxFilesPerUpload) * 5 MB (maxFileSize)
     */
    maxUploadSize: 104857600,
    /**
     * The width of the preview image.
     */
    previewImageWidth: 120,
    /**
     * The height of the preview image.
     */
    previewImageHeight: 120,
    /**
     * The maximum file size for preview images.
     */
    maxPreviewImageSize: 5242880,
    /**
     * The maximum file size for audio previews.
     */
    maxPreviewAudioSize: 15728640,
    /**
     * The maximum file size for video previews.
     */
    maxPreviewVideoSize: 524288000,
    /**
     * Default processing queue.
     */
    processingQueue: [
      'image',
      'audio',
      'video'
    ],
    /**
     * Optional selector for an upload all button.
     */
    uploadAllBtn: false,
    /**
     * Optional selector for a cancel all button.
     */
    cancelAllBtn: false,

    messages: {
      fileExtNotAllowed: 'The file extension ".{0}" is not allowed.',
      mimeTypeNotAllowed: 'The mimetype "{0}" is not allowed.',
      fileTooSmall: 'File {0} is too small. (min: {1})',
      fileTooBig: 'File {0} is too big. (max: {1})',
      tooManyFiles: 'You selected too many files in one go. (max: {0} Files)',
      uploadSizeExceeded: 'Your selected files exceed the maximum upload limit. (max: {0})'
    }
  };

})(jQuery, window, document);
