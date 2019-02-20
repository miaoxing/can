<?php $view->layout() ?>

<!-- /.page-header -->
<div class="page-header">
  <a class="btn pull-right btn-success" href="<?= $url('admin/roles/new') ?>">添加角色</a>

  <h1>
    角色管理
  </h1>
</div>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <table id="record-table" class="record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>名称</th>
          <th class="t-10">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <a href="<%= $.url('admin/roles/%s/edit', id) %>" title="编辑">
      <i class="fa fa-edit bigger-130"></i>
    </a>
    <a class="text-danger delete-record" href="javascript:;"
      data-href="<%= $.url('admin/roles/%s?_method=DELETE', id) %>" title="删除">
      <i class="fa fa-trash-o bigger-130"></i>
    </a>
  </div>
</script>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/data-table', 'jquery-unparam', 'form'], function () {
    var recordTable = $('#record-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/roles.json')
      },
      columns: [
        {
          data: 'name'
        },
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('table-actions', full)
          }
        }
      ]
    });

    recordTable.deletable();
  });
</script>
<?= $block->end() ?>
