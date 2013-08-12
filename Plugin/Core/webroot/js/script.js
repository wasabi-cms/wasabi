$(function() {

  $('.toggle-nav').click(function(event) {
    var $body = $('body');
    if ($body.hasClass('nav-closed')) {
      $body.removeClass('nav-closed');
    } else {
      $body.addClass('nav-closed');
    }
  });

  $('.main-nav > li').each(function() {
    $(this).hover(function() {
      if (!$(this).parent().hasClass('collapsed')) {
        return;
      }
      $(this).addClass('popout');
    }, function() {
      $(this).removeClass('popout');
    });
    if ($(this).find('ul').length) {
      $(this).find('> a').click(function() {
        $(this).parent().toggleClass('open');
      });
    }
  });

  var resizeTimer;
  var collapseMenu = function() {
    $('ul.main-nav').toggleClass('collapsed', $('ul.main-nav > li > a > span.item-name').first().css('display') === 'none');
  };
  collapseMenu();
  $(window).on('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(collapseMenu, 100);
  });

  $('.tabs').tabify();

  $('[data-toggle="dropdown"]').dropdown();

  $('[data-toggle="modal"]').livequery(function() {
    $(this).modal();
  });

  $('#languages')
    .tSortable({
      handle: 'a.sort',
      placeholder: 'sortable-placeholder',
      opacity: 0.8
    })
    .on('tSortable-change', function() {
      var i = 1;
      $(this).find('tbody > tr').each(function() {
        $(this).find('input.position').first().val(i);
        i++;
      });
      var form = $('#LanguageIndexForm');
      $.ajax({
        url: form.attr('action'),
        data: form.serialize(),
        type: 'post'
      });
    });

  $('.is-sortable')
    .tSortable({
      handle: 'a.sort',
      placeholder: 'sortable-placeholder',
      opacity: 0.8
    })
    .on('tSortable-change', function() {
      var i = 1;
      $(this).find('tbody > tr').each(function() {
        $(this).find('input[id*="Position"]').first().val(i);
        i++;
      });
    });

  $('#menu-items').on('change', '.menu-item-select', function(event) {
    var val = $(this).val();
    var parts = val.split('|');
    var $td = $(this).parent().parent();
    var _i, _len, url;

    function parseUrlString(urlString) {
      var url = {
        plugin: '',
        controller: '',
        action: '',
        params: [],
        query: ''
      };

      var parts = urlString.split('?');
      urlString = parts[0];

      if (parts[1] !== undefined) {
        url.query = parts[1];
      }

      parts = urlString.split('/');

      for (_i = 0, _len = parts.length; _i < _len; _i++) {

        if (_i === 0) {
          var plugin = parts[_i].split('plugin:')[1];
          if (plugin !== undefined) {
            url.plugin = plugin;
            continue;
          }
        }

        if ((_i === 0 || _i === 1) && url.controller === '') {
          var controller = parts[_i].split('controller:')[1];
          if (controller !== undefined) {
            url.controller = controller;
            continue;
          }
        }

        if ((_i ===1 || _i === 2) && url.action === '') {
          var action = parts[_i].split('action:')[1];
          if (action !== undefined) {
            url.action = action;
            continue;
          }
        }

        url.params.push(parts[_i]);
      }

      if (url.params.length > 0) {
        url.params = url.params.join('/');
      } else {
        url.params = '';
      }

      return url;
    }

    $td.find('input[name*="type"]').val(parts[0]);

    var $active_div = $td.find('div.active');
    $active_div.removeClass('active').find('input').prop('disabled', true);

    var $div = $td.find('div[data-type="' + parts[0] + '"]');
    $div.find('input').removeAttr('disabled');
    $div.addClass('active');

    if (parts[0] === 'Object' || parts[0] === 'Action') {
      $div = $td.find('div[data-type="' + parts[0] + '"]');
      url = {};

      if (parts[0] === 'Object') {
        url = parseUrlString(parts[3]);
        $div.find('input[name*="foreign_model"]').first().val(parts[1]);
        $div.find('input[name*="foreign_id"]').first().val(parts[2]);
      }

      if (parts[0] === 'Action') {
        url = parseUrlString(parts[1]);
      }

      $div.find('input[name*="plugin"]').first().val(url.plugin);
      $div.find('input[name*="controller"]').first().val(url.controller);
      $div.find('input[name*="action"]').first().val(url.action);
      $div.find('input[name*="params"]').first().val(url.params);
      $div.find('input[name*="query"]').first().val(url.query);
    }

    console.log($(this).val());
  });

  $('a.add-item').click(function(event) {
    event.preventDefault();
    var tr = $(this).parent().parent().find('table tr.new');
    var str = tr.html();
    var time = new Date().getTime().toString();
    str = str.split('{UID}').join(time);
    tr.clone().html(str).removeClass('new').appendTo(tr.parent());
    var table = $(this).parent().parent().find('table').first();
    var i = 1;
    table.find('tbody tr').each(function(index) {
      if ($(this).hasClass('new')) {
        return true;
      }
      $(this).find('input[id*="Position"]').first().val(i);
      i++;
    });
  });

  $('.list').on('click', 'a.remove-item', function(event) {
    event.preventDefault();
    var tr = $(this).parent().parent();
    var deleteInput = tr.find('input[id*="Delete"]');
    if (deleteInput.length) {
      deleteInput.first().val(1);
      tr.hide();
    } else {
      tr.hide(0, function() {
        $(this).remove();
      });
    }
    var i = 1;
    tr.parent().parent().find('tbody tr').each(function(index) {
      $(this).find('input[id*="Position"]').first().val(i);
      i++;
    });
  });

  $('.permissions .single-submit').click(function(event) {
    event.preventDefault();
    var that = $(this);
    that.hide();
    that.blur();
    that.parent().spin('small');
    $.ajax({
      type: 'POST',
      url: $('#GroupPermissionIndexForm').attr('action'),
      data: $(this).parent().parent().find('input').serialize(),
      cache: false,
      success: function() {
        var tr = that.parent().parent();
        var bgColor = that.parent().css('backgroundColor');
        that.parent().spin(false);
        that.show();
        tr.find('td:not(.controller)').stop().css({
          backgroundColor: '#fff7d9'
        }).animate({
            backgroundColor: bgColor
          }, 1000, function() {
            $(this).css({backgroundColor: ''});
          });
      }
    })
  });

  $('.tab-wrapper').tabify();

  /**
   * Default Ajax options
   */
  $.ajaxSetup({
    dataType: 'json'
  });

  /**
   * Default ajaxSuccess handler
   * display a flash message if the response contains
   * {
   *   'status': '...' # the class of the flash message
   *   'flashMessage': '...' # the text of the flash message
   * }
   */
  $(document).ajaxSuccess(function(event, xhr, settings) {
    if (xhr.status == 200 && xhr.statusText == 'OK') {
      var data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.status !== 'undefined' && typeof data.flashMessage !== 'undefined') {
        $.flashMessage('div.title-pad', data.status, data.flashMessage);
      }
    }
  });

  /**
   * Default ajaxError handler
   */
  $(document).ajaxError(function(event, xhr, settings) {
    var data;
    if (xhr.status == 401) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        if (confirm(data.name)) {
          window.location.reload();
        } else {
          window.location.reload();
        }
      }
    }
    if (xhr.status == 500) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        $.flashMessage('div.title-pad', 'error', data.name);
      }
    }
  });

  $.extend({

    translateEntity: function(entity) {
      if (wasabiTranslations[entity] !== undefined) {
        return wasabiTranslations[entity];
      } else {
        return entity;
      }
    },

    /**
     * Render a flash message
     *
     * @param elAfter The element after which the message should be rendered.
     * @param cls The css class of the flash message.
     * @param message The content of the flash message.
     */
    flashMessage: function(elAfter, cls, message) {
      var ancestor = $(elAfter);
      if (ancestor.length) {
        $('#flashMessage').remove();
        var flashMessage = $('<div id="flashMessage"></div>');
        flashMessage.addClass(cls).html(message);
        ancestor.after(flashMessage);
      }
    }

  });

  $.fn.spin.presets = {
    "small": { lines: 12, length: 0, width: 3, radius: 6 },
    "medium": { lines: 9, length: 4, width: 2, radius: 3 },
    "large": { lines: 11, length: 7, width: 2, radius: 5 }
  };

});
