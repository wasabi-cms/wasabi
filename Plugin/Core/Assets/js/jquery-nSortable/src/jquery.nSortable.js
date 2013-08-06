(function($) {
  "use strict";

  var NestedSortable = function(el, options) {
    this.$el = $(el);
    this.settings = $.extend({}, $.fn.nSortable.defaults, options);

    this.isDragging = false;
    this.$li = null;
    this.$clone = null;
    this.$placeholder = null;

    this.startPosition = {};
    this.startDepth = null;
    this.targetDepth = null;
    this.liOffset = {};
    this.delta = {
      x: 0,
      y: 0
    };

    this.placeholderHeight = null;
    this.placeholderDepth = null;

    this.$scrollParent = null;

    this._primaryEvents = [];
    this._secondaryEvents = [];

    this._results = [];

    this._buildEvents();
    $.attachEvents(this._primaryEvents);
  };

  NestedSortable.prototype = {

    _buildEvents: function() {
      this._primaryEvents = [
        [this.$el, 'li ' + this.settings.containerElement + ' ' + this.settings.handle, [
          ['mousedown', $.proxy(this._onMouseDown, this)],
          ['mouseup', $.proxy(this._onMouseUp, this)]
        ]]
      ];

      this._secondaryEvents = [
        [$(window), {
          mousemove: $.proxy(this._onMouseMove, this)
        }]
      ];
    },

    _onMouseDown: function(event) {
      event.preventDefault();
      this._initStart(event);
      $.attachEvents(this._secondaryEvents);
    },

    _onMouseUp: function(event) {
      var that = this;
      $.detachEvents(this._secondaryEvents);
      if (this.isDragging) {
        if (this.settings.animateTarget) {
          this.$clone.insertBefore(this.$placeholder);
          var placeholderOffset = this.$placeholder.offset();
          this.$clone.animate({
            top: placeholderOffset.top,
            left: placeholderOffset.left
          }, parseInt(this.settings.animationLength), _stop);
        } else {
          _stop();
        }
      }

      function _stop() {
        that.$clone.remove();
        that.$clone = null;
        that.$li.insertBefore(that.$placeholder);
        that.$placeholder.remove();
        that.$placeholder = null;
        that.$li.show();
        that.$li = null;
        that.$scrollParent = null;
        that.isDragging = false;
        that._trigger('nSortable-change', event);
      }
    },

    _onMouseMove: function(event) {
      if (!this.isDragging) {
        this._initClone();
        this._initPlaceholder();
        this.$li.hide();
        this.$scrollParent = this.$clone.scrollParent();
        this.isDragging = true;
      }
      this._updateClonePosition(event);
      this._updateDelta(event);
      this._updateTargetDepth();
      this.placeholderDepth = this.$placeholder.parentsUntil(this.$el, 'ul').length;

      if (this.settings.scroll === true) {
        this._scroll(event);
      }

      var $lis = this.$placeholder.parent().find('> li').filter(function() {
        return ($(this).css('display') !== 'none' && $(this).css('position') !== 'absolute');
      });
      var placeholderIndex = $lis.index(this.$placeholder);
      var isLastItem = (placeholderIndex === $lis.length - 1);
      var hasPrevItem = (placeholderIndex > 0);

      (function(that) {
        if (that._yIntersectsPlaceholder(event)) {
          // move placeholder to parent list if it is the last item of it's current list
          if (isLastItem && (that.targetDepth < that.placeholderDepth)) {
            var $placeholderUl = that.$placeholder.parent();
            var $placeholderParentLi = $placeholderUl.parent();
            var $placeholderUlChildren = $placeholderUl.find('> li').filter(function() {
              return ($(this).css('display') !== 'none' && !$(this).hasClass(that.settings.placeholder) && $(this).css('position') !== 'absolute');
            });
            that.$placeholder.insertAfter($placeholderParentLi);
            if (!$placeholderUlChildren.length) {
              $placeholderParentLi.addClass(that.settings.noChildClass);
            }
            return;
          }

          // make the item a child of its prev item
          if (hasPrevItem && (that.targetDepth > that.placeholderDepth)) {
            var $prevItem = $($lis.get(placeholderIndex - 1));
            var $prevItemUl = $prevItem.find('> ul');
            if (!$prevItemUl.length) {
              $prevItemUl = $('<ul></ul>');
              $prevItem.append($prevItemUl);
            }
            $prevItemUl.append(that.$placeholder).parent().removeClass(that.settings.noChildClass);
          }
        } else {
          var $items = that.$el.find('li').filter(function() {
            return (
              ($(this)[0] !== that.$li[0]) &&
              ($(this).css('display') !== 'none') &&
              ($(this).css('position') !== 'absolute') &&
              !$(this).parents('li').filter(function() {
                return ($(this).css('position') === 'absolute') || $(this).is(that.$li)
              }).length &&
              !$(this).hasClass(that.settings.placeholder)
            );
          }).find(that.settings.containerElement);

          var $intersectedItem = null;
          var direction = null;

          $items.each(function() {
            var min = $(this).position().top;
            var max = min + $(this).outerHeight();
            var middle = parseInt((min + max) / 2);
            if (event.pageY >= min && event.pageY < middle) {
              $intersectedItem = $(this).parent();
              direction = 'up';
              return;
            }
            if (event.pageY > middle && event.pageY <= max ) {
              $intersectedItem = $(this).parent();
              direction = 'down';
              return;
            }
          });

          if ($intersectedItem === null) {
            return;
          }

          var intersectedItemDepth = $intersectedItem.parentsUntil(that.$el, 'ul').length;
          if (that.targetDepth !== intersectedItemDepth) {
            return;
          }

          if (that.targetDepth === intersectedItemDepth) {
            if (direction === 'up') {
              if ($intersectedItem.prev().hasClass(that.settings.placeholder)) {
                return;
              }
              $placeholderUl = that.$placeholder.parent();
              $placeholderParentLi = $placeholderUl.parent();
              $placeholderUlChildren = $placeholderUl.find('> li').filter(function() {
                return ($(this).css('display') !== 'none' && !$(this).hasClass(that.settings.placeholder) && $(this).css('position') !== 'absolute');
              });
              that.$placeholder.insertBefore($intersectedItem);
              if (!$placeholderUlChildren.length) {
                $placeholderParentLi.addClass(that.settings.noChildClass);
              }
              return;
            }
            if (direction === 'down') {
              if ($intersectedItem.next().css('display') === 'none' && $intersectedItem.next().next().hasClass(that.settings.placeholder)) {
                return;
              }
              $placeholderUl = that.$placeholder.parent();
              $placeholderParentLi = $placeholderUl.parent();
              $placeholderUlChildren = $placeholderUl.find('> li').filter(function() {
                return ($(this).css('display') !== 'none' && !$(this).hasClass(that.settings.placeholder) && $(this).css('position') !== 'absolute');
              });
              that.$placeholder.insertAfter($intersectedItem);
              if (!$placeholderUlChildren.length) {
                $placeholderParentLi.addClass(that.settings.noChildClass);
              }
            }
          }
        }
      })(this);

      // remove empty uls
      this.$el.find('ul').not(':has(li)').remove();
    },

    _initStart: function(event) {
      this.$li = $(event.target).closest('li');

      this.startPosition = {
        x: event.pageX,
        y: event.pageY
      };

      this.startDepth = this.$li.parents('ul').length - 1;

      var liOffset = this.$li.offset();
      this.liOffset = {
        x: this.startPosition.x - liOffset.left,
        y: this.startPosition.y - liOffset.top
      };
    },

    _initClone: function() {
      this.$clone = this.$li.clone();
      this.$clone.css({
        display: 'list-item',
        width: this.$li.outerWidth(),
        height: this.$li.outerHeight(),
        position: 'absolute',
        zIndex: 10000,
        opacity: this.settings.opacity
      });
      this.$li.parent().append(this.$clone);
    },

    _initPlaceholder: function() {
      this.placeholderHeight = this.$li.innerHeight();
      this.$placeholder = $('<li></li>');
      this.$placeholder
        .addClass(this.settings.placeholder)
        .css({
          height: this.placeholderHeight
        });
      this.$li.after(this.$placeholder);
    },

    _updateClonePosition: function(event) {
      this.$clone.css({
        left: event.pageX - this.liOffset.x,
        top: event.pageY - this.liOffset.y
      });
    },

    _updateDelta: function(event) {
      this.delta.x = -(this.startPosition.x - event.pageX);
      this.delta.y = this.startPosition.y - event.pageY;
    },

    _updateTargetDepth: function() {
      var diff = parseInt(this.delta.x / this.settings.tabWidth);
      var targetDepth = this.startDepth + diff;
      targetDepth = Math.max(targetDepth, 0);
      targetDepth = Math.min(targetDepth, this.settings.maxDepth);

      this.targetDepth = targetDepth;
    },

    _scroll: function(event) {
      var sParent = this.$scrollParent[0], overflowOffset = this.$scrollParent.offset();
      if (sParent != document && sParent.tagName != 'HTML') {
        if ((overflowOffset.top + sParent.offsetHeight - event.pageY) < this.settings.scrollSensitivity) {
          sParent.scrollTop = sParent.scrollTop + this.settings.scrollSpeed;
        } else if (event.pageY - overflowOffset.top < this.settings.scrollSensitivity) {
          sParent.scrollTop = sParent.scrollTop - this.settings.scrollSpeed;
        }
        if ((overflowOffset.left + sParent.offsetWidth - event.pageX) < this.settings.scrollSensitivity) {
          sParent.scrollLeft = sParent.scrollLeft + this.settings.scrollSpeed;
        } else if(event.pageX - overflowOffset.left < this.settings.scrollSensitivity) {
          sParent.scrollLeft = sParent.scrollLeft - this.settings.scrollSpeed;
        }
      } else {
        var $doc = $(document), $win = $(window);
        if (event.pageY - $doc.scrollTop() < this.settings.scrollSensitivity) {
          $doc.scrollTop($doc.scrollTop() - this.settings.scrollSpeed);
        } else if ($win.height() - (event.pageY - $doc.scrollTop()) < this.settings.scrollSensitivity) {
          $doc.scrollTop($doc.scrollTop() + this.settings.scrollSpeed);
        }
        if (event.pageX - $doc.scrollLeft() < this.settings.scrollSensitivity) {
          $doc.scrollLeft($doc.scrollLeft() - this.settings.scrollSpeed);
        } else if ($win.width() - (event.pageX - $doc.scrollLeft()) < this.settings.scrollSensitivity) {
          $doc.scrollLeft($doc.scrollLeft() + this.settings.scrollSpeed);
        }
      }
    },

    _yIntersectsPlaceholder: function(event) {
      var min = this.$placeholder.offset().top;
      var max = min + this.placeholderHeight;
      return (event.pageY >= min && event.pageY <= max);
    },

    _trigger: function(eventType, eventOrigin) {
      this.$el.trigger(eventType, {
        event: eventOrigin,
        toArray: $.proxy(this.toArray, this),
        serialize: $.proxy(this.serialize, this)
      });
    },

    _recursiveArray: function($item, depth, left) {
      var that = this,
        right = left + 1,
        id,
        parentId,
        $children = $item.find('> ul > li'),
        tmpItem = {};

      if ($children.length) {
        depth++;
        $children.each(function () {
          right = that._recursiveArray($(this), depth, right);
        });
        depth--;
      }

      id = $item.attr(that.settings.dataAttribute);

      if (depth === 1) {
        parentId = 0;
      } else {
        parentId = $item.parent().parent().attr(that.settings.dataAttribute);
      }

      if (id) {
        tmpItem = {
          "id": id,
          "parent_id": parentId,
          "depth": depth
        };
        tmpItem[this.settings.leftKey] = left;
        tmpItem[this.settings.rightKey] = right;
        this._results.push(tmpItem);
      }

      left = right + 1;
      return left;
    },

    serialize: function() {
      var results = this.toArray(), str = [], _i = 0, _len = results.length;

      for (; _i < _len; _i++) {
        var item = results[_i];
        var baseStr = this.settings.serializeKey + '[' + _i + ']';
        for (var attr in item) {
          str.push(baseStr + '[' + attr + ']=' + item[attr]);
        }
      }

      return str.join('&');
    },

    toArray: function() {
      var left = 1, that = this;

      this._results = [];

      this.$el.children('li').each(function () {
        left = that._recursiveArray($(this), 1, left);
      });

      this._results = this._results.sort(function(a,b){ return (a.left - b.left); });

      return this._results;
    }
  };

  $.fn.nSortable = function(options) {
    if (!options || typeof options === 'object') {
      return this.each(function() {
        if (!$(this).data('nSortable')) {
          $(this).data('nSortable', new NestedSortable(this, options));
        }
      });
    } else if (typeof options === 'string' && options.charAt(0) !== '_') {
      var nSortable = this.data('nSortable');
      if (!nSortable) {
        throw new Error('nSortable is not initialized on this DOM element.');
      }
      if (nSortable && nSortable[options]) {
        return nSortable[options].apply(nSortable, Array.prototype.slice.apply(arguments, [1]))
      }
    }
    throw new Error('"' + options + '" is no valid api method.');
  };

  $.fn.nSortable.defaults = {
    handle: 'div',
    tabWidth: 20,
    containerElement: '> div',
    opacity: 0.6,
    placeholder: 'placeholder',
    noChildClass: 'no-children',
    dataAttribute: 'data-node-id',
    maxDepth: Infinity,
    animateTarget: true,
    animationLength: 300,
    scroll: true,
    scrollSensitivity: 20,
    scrollSpeed: 20,
    serializeKey: 'nodes',
    leftKey: 'left',
    rightKey: 'right'
  };

})(jQuery);
