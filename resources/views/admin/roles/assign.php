<?php $view->layout() ?>

<div class="page-header">
  <h1>
    角色管理
    <small>
      <i class="fa fa-angle-double-right"></i>
      分配角色
    </small>
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <form id="role-form" class="form-horizontal" method="post" role="form">

      <div class="form-group">
        <label class="col-lg-2 control-label" for="username">
          用户
        </label>

        <div class="col-lg-4">
          <p class="form-control-static"><?= $e($user->getNickName()) ?></p>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          角色
        </label>

        <div class="col-lg-4">
          <select class="form-control" name="roles[]" id="roles" multiple>
          </select>
        </div>

        <label class="col-lg-6 help-text" for="roles">按住Ctrl选中多项，按住Shift跨范围选择。</label>
      </div>

      <input type="hidden" name="id" id="id">
      <input type="hidden" name="_method" value="PUT">

      <div class="clearfix form-actions form-group">
        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/admins') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回
          </a>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/form', 'ueditor', 'plugins/admin/js/data-table', 'plugins/app/js/validation'], function (form) {
    form.toOptions(
      $('#roles'),
      <?= json_encode($roles->toArray()) ?>,
      'id',
      'name',
      <?= json_encode($selectedRoleIds) ?>
    );

    $('#role-form')
      .ajaxForm({
        dataType: 'json',
        beforeSubmit: function (arr, $form, options) {
          return $form.valid();
        },
        success: function (ret) {
          $.msg(ret, function () {
            if (ret.code === 1) {
              window.history.back();
            }
          });
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
