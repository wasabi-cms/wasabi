$(function() {

  function set_closed_pages_cookie() {
    var closed_pages = [];
    $('#pages').find('li.closed').each(function() {
      closed_pages.push($(this).attr('data-cms-page-id'));
    });
    $.cookie('closed_pages', closed_pages, {expires: 365});
  }

  var $pages = $('#pages');

  $pages.on('click', '.expander', function(event) {
    event.preventDefault();
    var $expander = $(this);
    var $li = $expander.parent().parent().parent().parent();
    if (!$li.hasClass('closed')) {
      $expander.removeClass('wicon-collapse').addClass('wicon-expand');
      $li.find('ul').first().slideUp(300, function() {
        $li.addClass('closed');
        set_closed_pages_cookie();
      });
    } else {
      $expander.removeClass('wicon-expand').addClass('wicon-collapse');
      $li.find('ul').first().slideDown(300, function() {
        $li.removeClass('closed');
        set_closed_pages_cookie();
      });
    }
  });

  $pages.nSortable({
    handle: 'a.move',
    placeholder: 'sortable-placeholder',
    dataAttribute: 'data-cms-page-id',
    serializeKey: 'CmsPage',
    leftKey: 'lft',
    rightKey: 'rght'
  });

  $pages.on('nSortable-change', function(event, nSortable) {
    var url = $pages.attr('data-reorder-url');
    if (typeof url === 'undefined' || url === 'false') {
      return;
    }
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: url,
      data: nSortable.serialize(),
      cache: false
    });
  });

  var $routes = $('.field.routes');

  function submitRoute() {
    var url = $routes.find('table.routes').attr('data-add-route-url');
    var pageId = $('#CmsPageId').val();
    var routeUrl = $('#RouteUrl').val();
    if (typeof url === 'undefined' || typeof pageId === 'undefined' || routeUrl === '') {
      return;
    }
    $routes.block({
      backgroundColor: '#fff'
    });
    $.ajax({
      type: 'post',
      url: url,
      data: {
        "Route[url]": routeUrl,
        "Route[type]": $('#RouteType').val(),
        pageId: pageId
      },
      dataType: 'json',
      success: function(data) {
        $routes.unblock();
        $routes.html(data);
      }
    });
  }

  $('#CmsPageEditForm').on('submit', function(event) {
    var id = $(event.originalEvent.explicitOriginalTarget).attr('id');
    if (id === 'RouteUrl' || id === 'RouteType') {
      event.preventDefault();
      submitRoute();
    }
  });

  $routes.on('click', '.new-route button[type="submit"]', function(event) {
    event.preventDefault();
    submitRoute();
  });

  $routes.on('beforeAjax.modal', function(event) {
    $routes.block({
      backgroundColor: '#fff'
    });
  });

  $routes.on('deleteRoute', function(event, data) {
    $routes.unblock();
    $routes.html(data);
  });

  $routes.on('makeDefaultRoute', function(event, data) {
    $routes.unblock();
    $routes.html(data);
  });

  var $layoutAttributes = $('.field.layout-attributes');

  $('#CmsPageCmsLayoutId').on('change', function(event) {
    var url = $layoutAttributes.attr('data-change-url');
    if (!url) {
      return;
    }
    var $select = $(this);
    var $pageId = $('#CmsPageId');
    if ($pageId.length > 0) {
      $pageId = '/' + $pageId.val();
    } else {
      $pageId = '';
    }
    $layoutAttributes.block({
      backgroundColor: '#fff',
      onBlock: function() {
        $.ajax({
          url: url + '/' + $select.val() + $pageId,
          method: 'get',
          success: function(data) {
            $layoutAttributes.unblock(function() {
              $layoutAttributes.html(data);
            });
          }
        });
      }
    });
  });
});
