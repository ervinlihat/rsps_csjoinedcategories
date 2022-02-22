{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<table class="table tableDnD" style="width:100%;border-collapse:collapse;">
<tr style="background-color: #f1f1f1;">
    
    <th>{l s='TITLE' mod='categoryjoin'}</th>
    <th>{l s='A- CATEGORY' mod='categoryjoin'}</th>
    
    <th>{l s='B-CATEGORY' mod='categoryjoin'}</th>
    <th>{l s='Action' mod='categoryjoin'}</th>
</tr>

{if $sql_query|@count}
 <form action="#" method="post" id="dform">
{foreach from=$sql_query item=row}

<tr>
    
    <td>
        <b class="totprod">{$row.title|escape:'htmlall':'UTF-8'}</b><br/>
    </td>
    <td>
        {$row.id_category_A|escape:'htmlall':'UTF-8'} 
    </td>
    <td>
        {$row.id_category_B|escape:'htmlall':'UTF-8'}
    </td>
    <td>
    
  
     
	  <input type="button" name="delete" onclick="$('#deleteItem').val({$row.id});$('#dform').submit();" value="Delete" />

    
    </td>
    
</tr>
{/foreach}
 <input type="hidden" id="deleteItem" name="deleteItem" value="0"/>
</form> 
{else}
<tr>
<td colspan="6">
    <div class="warn">{l s='No alert was registered' mod='totshowmailalerts'}</div>
</td>
</tr>
{/if}
</table>

<h2>
Update configuration
</h2>
<div>
<form action ="#" method ="POST">
<label for="css">Title</label>
  <input type="text" id="css" name="title" >
  <br>
  <label for="javascript">Cat A</label>
  <input type="text" id="javascript" name="cat_A" >
  <br>
  <label for="catb">Cat A</label>
  <input type="text" id="catb" name="cat_B" >
  <br>
  <input type="submit" name="add" value="Add">
</form>
</div>