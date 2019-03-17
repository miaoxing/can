<script type="text/html" id="permissionPickerTpl">
  <div class="form-group">
    <label class="col-lg-2 control-label">
      页面访问权限
    </label>

    <div class="col-lg-8 js-permission-list">
      <div class="permission-namespace-checkbox checkbox-inline">
        <label class="mb-0">
          <input type="checkbox" class="js-permission-namespace" name="permissionIds[]" value="admin" <%= permissionIds.indexOf('admin') != -1 ? 'checked' : '' %>>
          <strong>全部</strong>
        </label>
      </div>
      <ul class="permission-namespace-list list-unstyled">
        <% $.each(pages.admin.controllers, function (j, controller) { %>
        <li>
          <div class="checkbox-inline permission-controller">
            <label class="mb-0">
              <input type="checkbox" class="js-permission-controller" name="permissionIds[]" value="<%= controller.value %>" <%= permissionIds.indexOf(controller.value) != -1 ? 'checked' : '' %>>
              <strong><%= controller.name %></strong>
            </label>
          </div>
          <div class="permission-actions">
            <% for (var action in controller.actions) { %>
            <div class="checkbox-inline permission-action-checkbox">
              <label class="mb-0">
                <input type="checkbox" class="js-permission-action" name="permissionIds[]" value="<%= action %>" <%= permissionIds.indexOf(action.split(',')[0]) != -1 ? 'checked' : '' %>>
                <%= controller.actions[action] %>
              </label>
            </div>
            <% } %>
          </div>
          <div class="clearfix"></div>
        </li>
        <% }) %>
      </ul>
    </div>
  </div>
</script>
