<?php $view->layout() ?>

<?= $block('css') ?>
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

      <div class="permission-form-groups">
        <div class="col-lg-offset-2 bigger-110">
          &nbsp; <i class="fa fa-spinner fa-spin"></i> 权限加载中,请稍等...
        </div>
      </div>

      <input type="hidden" name="id" id="id">
      <input type="hidden" name="_method" value="<?= $role->getHttpMethod() ?>">

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-info" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn" href="<?= $url('admin/roles') ?>">
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

<?= $block('js') ?>
<script>
  require([
    'plugins/can/js/admin/permissions',
    'form', 'ueditor', 'jquery-deparam', 'dataTable', 'validator'
  ], function (permissions) {
    var permissionIds = <?= json_encode($permissionIds) ?>;

    permissions.picker({
      $el: $('.permission-form-groups'),
      permissionIds: permissionIds
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
