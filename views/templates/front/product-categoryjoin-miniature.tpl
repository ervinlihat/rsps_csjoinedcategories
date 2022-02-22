{* <div class="categoryjoin-title">

{foreach from=$accesorii_title item=item }

    <h3> {$item.title}</h3>

{/foreach}</div> *}
<div>
{foreach from=$product_FromCat_B  item=item}
    
    <h3 class="section-title">{$item.title}</h3>

    <div class="categoryjoin-container">

        {foreach from=$item.product_details item=prod}
            
            <div
                class="product-miniature product-miniature-default product-miniature-grid product-miniature-layout-1 js-product-miniature">

                <span><a href="{$urls.base_url}{$prod.id_parent_default}/{$prod.link_rewrite}.html"><img
                        src='{$urls.base_url}{$prod.id_image}-home_default/{$prod.link_rewrite}.jpg' /></a></span>

                <h3 class="product-title">{$prod.name}</h3>

                <div class="product-price"><span>{$prod.price|string_format:"%.2f "}</span></div>

                <div class="product-add-cart">
                
                    {if $prod.quantity > 0 }

                        <form
                            action="{$urls.base_url}cart?add=1&id_product={$prod.id_product}&id_product_attribute={$prod.id_product_attribute}&token={$static_token}"
                            method="post">

                            <input type="hidden" name="id_product" value="{$prod.id_product}">

                            <div class="input-group-add-cart">


                                <input type="number" name="qty"
                                    value="1"
                                    class="form-control input-qty"
                                    min="0">

                                <button class="btn btn-product-list add-to-cart" data-button-action="add-to-cart" type="submit"><i
                                        class="fa fa-shopping-bag fa-fw bag-icon" aria-hidden="true"></i> <i
                                        class="fa fa-circle-o-notch fa-spin fa-fw spinner-icon" aria-hidden="true"></i>

                                    {l s='Add to cart' d='Shop.Theme.Actions'}

                                </button>

                            </div>

                        </form>
                    {else}
                        
                        <a href="{$urls.base_url}{$prod.id_parent_default}/{$prod.link_rewrite}.html" class="btn btn-product-list"> {l s='View' d='Shop.Theme.Actions'}

                        </a>

                    {/if}

                </div>

            </div>

        {/foreach}

    </div>
    
{/foreach}
</div>
