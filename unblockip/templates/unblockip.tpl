{if !empty($alerts)}
<div class="alert alert-success textcenter">
    <p>{$alerts}</p>
</div>
{/if}
{if !empty($errors)}
<div class="alert alert-danger textcenter">
    <p>{$errors}</p>
</div>
{/if}
<form name='ipaddresscheck' method='POST' action='{$modulelink}'>
<div class="panel panel-default">
  <div class="panel-heading"><h3 class="panel-title">{$unblock_lang.address_to_check}</h3></div>
  <div class="panel-body">
     <div class="form-group">
     	  <input type="text" size="25" name="ip_address" style="text-align: center;" value="{$smarty.server.REMOTE_ADDR}"  class="form-control">
        </div>
    </div>  
    <div class="panel-footer">
	              <input type="hidden" name="action" value="remove_ip_block" />
          <input class="btn btn-primary" type="submit" name="unblock" value="{$unblock_lang.check_for_ip}" />
    </div>  
</div>
</form>
