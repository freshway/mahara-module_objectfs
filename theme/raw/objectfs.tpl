{include file='header.tpl'}

{if $sitedata}
  <div id="site-stats-wrap" class="panel-items js-masonry" data-masonry-options='{ "itemSelector": ".panel" }'>
    <div class="panel panel-info">
      <h3 class="panel-heading"> {str tag=object_status:location section=module.objectfs} <span class="icon icon-info pull-right" role="presentation" aria-hidden="true"></span></h3>

      <table class="table">
        <tr>
          <th>Location</th>
          <th>Objectcount</th>
          <th>Objectsum</th>
        </tr>

        {foreach $sitedata['location'] key item}
          <tr>
            <td>{$item->datakey}</td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectcount}%; background: #17a5eb;">{$item->objectcount}</div></td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectsum}%; background: #17a5eb;">{$item->objectsum|display_size}</div></td>
          </tr>
        {/foreach}

        <tr>
          <td><a href="{$WWWROOT}admin/extensions/pluginconfig.php?plugintype=module&pluginname=objectfs">Settings</a></td>
        </tr>
      </table>

    </div>

    <div class="panel panel-info">
      <h3 class="panel-heading"> {str tag=object_status:fileranges section=module.objectfs} <span class="icon icon-info pull-right" role="presentation" aria-hidden="true"></span></h3>

      <table class="table">
        <tr>
          <th>File sizes</th>
          <th>Objectcount</th>
          <th>Objectsum</th>
        </tr>

        {foreach $sitedata['log_size'] key item}
          <tr>
            <td>{$item->datakey}</td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectcount}%; background: #17a5eb;">{$item->objectcount}</div></td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectsum}%; background: #17a5eb;">{$item->objectsum|display_size}</div></td>
          </tr>
        {/foreach}

      </table>

    </div>

    <div class="panel panel-info">
      <h3 class="panel-heading"> {str tag=object_status:mimetypes section=module.objectfs} <span class="icon icon-info pull-right" role="presentation" aria-hidden="true"></span></h3>

      <table class="table">
        <tr>
          <th>Mimetype</th>
          <th>Objectcount</th>
          <th>Objectsum</th>
        </tr>

        {foreach $sitedata['mime_type'] key item}
          <tr>
            <td>{$item->datakey}</td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectcount}%; background: #17a5eb;">{$item->objectcount}</div></td>
            <td><div class="ofs-bar" style="width:{$item->relativeobjectsum}%; background: #17a5eb;">{$item->objectsum|display_size}</div></td>
          </tr>
        {/foreach}

      </table>

    </div>

  </div>
{/if}


{include file='footer.tpl'}
