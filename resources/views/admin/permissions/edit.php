<?php $view->layout() ?>

<div class="page-header">
  <h1>
    权限管理
    <small>
      <i class="fa fa-angle-double-right"></i>
      权限管理
    </small>
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <form id="permission-form" class="form-horizontal" method="post" permission="form">

      <div class="form-group">
        <label class="col-lg-2 control-label" for="id">
          <span class="text-warning">*</span>
          编号
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="id" id="id" data-rule-required="true">
        </div>

        <label class="col-lg-6 help-text" for="id">
          编号由数字或英文字母组成
        </label>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          名称
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="name" id="name" data-rule-required="true">
        </div>
      </div>

      <input type="hidden" name="_method" value="<?= $permission->getHttpMethod() ?>">

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/permissions') ?>">
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

<?= $block('js') ?>
<script>
  require(['form', 'ueditor', 'jquery-deparam', 'dataTable', 'validator'], function () {
    $('#permission-form')
      .loadJSON(<?= $permission->toJson() ?>)
      .loadParams()
      .ajaxForm({
        url: $.url('admin/permissions'),
        dataType: 'json',
        beforeSubmit: function (arr, $form, options) {
          return $form.valid();
        },
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/permissions');
            }
          });
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
