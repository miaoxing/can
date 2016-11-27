define(['template'], function (template) {
  var Permissions = function () {
    // do nothing.
  };

  $.extend(Permissions.prototype, {
    $el: $('body'),
    $: function (selector) {
      return this.$el.find(selector);
    },

    // 权限选择器表单控件
    permissionIds: [],
    picker: function (options) {
      $.extend(this, options);

      var that = this;

      // 渲染权限表单控件
      $.getJSON($.url('admin/permissions/all.json'), function (ret) {
        if (ret.code !== 1) {
          $.msg(ret);
          return;
        }

        ret.permissionIds = that.permissionIds;
        that.$el.html(template.render('permissionPickerTpl', ret));

        // 更改控制器,更新所有操作为一样的值
        that.$('.js-permission-controller').change(function () {
          changeController(this);
        });

        // 更改命名空间,更新所有控制器和操作为一样的值
        that.$('.js-permission-namespace').change(function () {
          changeNamespace(this);
        });

        // 为控制器设置选中状态
        changeController(that.$('.js-permission-controller:checked'));
        changeNamespace(that.$('.js-permission-namespace:checked'));

        function changeNamespace(checkbox) {
          var checked = $(checkbox).prop('checked');
          $(checkbox).closest('.js-permission-list')
            .find('.js-permission-controller, .js-permission-action')
            .prop('checked', checked)
            .prop('disabled', checked);
        }

        function changeController(checkbox) {
          var checked = $(checkbox).prop('checked');
          $(checkbox).closest('li')
            .find('.js-permission-action')
            .prop('checked', checked)
            .prop('disabled', checked);
        }
      });
    }
  });

  return new Permissions();
});
