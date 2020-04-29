{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='Fruit Count'}</span>
{/capture}

<h1 class="page-heading">{l s='Count your fruit here!'}</h1>
<p class="info-account">{l s='On this page you can add and remove fruit from your account.'}</p>
<p>Fruit type: {$fruit_type}</p>

<form method="post">
    <ul class="footer_links clearfix">
        <li>
            <button type="submit" class="button btn btn-default button-medium" name="decrement" {if $fruit_quantity == 0} disabled {/if}>
                <span>
                    <i class="icon-minus"></i>
                </span>
            </button>
        </li>
        <li>
            <h4>Fruit quantity: {$fruit_quantity}</h4></li>
        <li>
            <button type="submit" class="button btn btn-default button-medium" name="increment">
                <span>
                    <i class="icon-plus"></i>
                </span>
            </button>
        </li>
    </ul>
</form>

<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}">
            <span><i class="icon-chevron-left"></i> {l s='Home'}</span>
        </a>
    </li>
</ul>