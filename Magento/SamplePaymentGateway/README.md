## Success page and failed page example:

```html
    <p>SUCCESS</p>
    <p><button id="get-info">Get Order Info</button></p>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">// <![CDATA[

    // ]]></script>
    <script type="text/javascript">// <![CDATA[
    /* Get url params */
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    var orderId = getParameterByName('orderId');
    var auth_token = getParameterByName('auth_token');

    jQuery(document).ready(function(){
            function getOrderInfo(e) {
              jQuery.ajax({
                url: 'https://oauth-uat.flexiti.fi/flexiti/online-api/online/client-id/'+ 'flexitidemo' +'/notifications/'+ orderId ,
                headers: {
                  'Authorization': 'Bearer ' + auth_token,
                },
                type: 'get',
                success: function(data) {
                    console.log(data);
                },
                error: function(jqXhr, textStatus, errorThrown) {   
                    console.error(jqXhr, textStatus, errorThrown)            
                }
              });
              e.preventDefault();
            }

            jQuery('#get-info')
              .on('click', getOrderInfo);

    });
    // ]]></script>
```






