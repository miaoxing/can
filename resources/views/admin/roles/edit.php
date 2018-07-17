<?php $view->layout() ?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/can/css/admin/permissions.css') ?>"/>
<?= $block->end() ?>

<div class="page-header">
  <h1>
    角色管理
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <form class="js-role-form form-horizontal" method="post" role="form">

      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          名称
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="name" id="name" data-rule-required="true">
        </div>
      </div>

      <div class="js-permission-form-groups permission-form-groups">
        <div class="col-lg-offset-2 bigger-110">
          &nbsp; <i class="fa fa-spinner fa-spin"></i> 权限加载中,请稍等...
        </div>
      </div>

      <input type="hidden" name="id" id="id">
      <input type="hidden" name="_method" value="<?= $role->getHttpMethod() ?>">

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/roles') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<?php require $view->getFile('can:admin/permissions/picker.php') ?>

<?= $block->js() ?>
<script>
  require([
    'template',
    'form', 'ueditor', 'jquery-deparam', 'dataTable', 'validator'
  ], function (template) {
    var permissionIds = <?= json_encode($permissionIds) ?>;

    // 渲染权限表单控件
    $.getJSON($.url('admin/permissions/all.json'), function (ret) {
      if (ret.code !== 1) {
        $.msg(ret);
        return;
      }

      ret.permissionIds = permissionIds;
      $('.js-permission-form-groups').html(template.render('permissionPickerTpl', ret));

      // 更改控制器,更新所有操作为一样的值
      $('.js-permission-controller').change(function () {
        changeController(this);
        checkNamespace();
      });

      // 更改命名空间,更新所有控制器和操作为一样的值
      $('.js-permission-namespace').change(function () {
        changeNamespace(this);
      });

      $('.js-permission-action').change(function () {
        var checked = $(this).closest('.permission-actions').find(':checkbox:not(:checked)').length === 0;
        $(this).closest('li').find('.js-permission-controller').prop('checked', checked);

        checkNamespace();
      });

      // 为控制器设置选中状态
      changeController($('.js-permission-controller:checked'));
      changeNamespace($('.js-permission-namespace:checked'));

      function changeNamespace(checkbox) {
        var checked = $(checkbox).prop('checked');
        $(checkbox).closest('.js-permission-list')
          .find('.js-permission-controller, .js-permission-action')
          .prop('checked', checked);
      }

      function changeController(checkbox) {
        var checked = $(checkbox).prop('checked');
        $(checkbox).closest('li')
          .find('.js-permission-action')
          .prop('checked', checked);
      }

      function checkNamespace() {
        var checked = $('.js-permission-controller:not(:checked)').length === 0;
        $('.js-permission-namespace').prop('checked', checked);
      }
    });

    $('.js-role-form')
      .loadJSON(<?= $role->toJson() ?>)
      .loadParams()
      .ajaxForm({
        url: $.url('admin/roles'),
        dataType: 'json',
        beforeSubmit: function (arr, $form, options) {
          return $form.valid();
        },
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/roles');
            }
          });
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
