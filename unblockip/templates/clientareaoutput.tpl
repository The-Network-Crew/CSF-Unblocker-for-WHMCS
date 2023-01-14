{if $donotautounblock != "on"}
<div class="alert alert-danger textcenter">
    <p><strong>{$unblock_lang.ip_block_auto_removed}</strong></p>
    <p>
    {$unblock_lang.detailed_block_information}: <br>
    {$unblockip_alerts}</p>
</div>
{if $unblockip_errors}
<div class="alert alert-danger textcenter">
    <p><strong>{$unblock_lang.ip_auto_block_failed}<br>{$unblock_lang.please_open_a_support_ticket}</strong></p>
</div>
{/if}
{else}
<div class="alert alert-danger textcenter">
    <p><strong>
    {$unblockip_alerts}</p>
    <p><form name='ipaddresscheck' method='POST' action='index.php?m=unblockip'>
    <input type="text" size="25" name="ip_address" style="text-align: center;" value="{$smarty.server.REMOTE_ADDR}" class="form-control">
    <input type="hidden" name="action" value="remove_ip_block" /><br/>
    <input class="btn btn-primary" type="submit" name="unblock" value="{$unblock_lang.request_removal_of_block}" />
    </form>    
</div>
{/if}
{if $unblockip_errors}
<div class="alert alert-error textcenter">
      <p><strong>{$unblock_lang.ip_address_is_blocked}<br>{$unblock_lang.please_open_a_support_ticket}</strong></p>
</div>
{/if}
