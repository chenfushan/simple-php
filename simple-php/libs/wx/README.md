# Wx library for develop wechat public account

> because of times limit for getGlobalAccessToken and getJsiApiTicket, you should save the result in cache

1. create wx button for wechat public account

```
createButton($buttonJson, $globalAccessToken)
```
 - globalAccessToken

 > get from wx

 - buttonJson

 > {
 >   "button": [
 >   {
 >           "name": "Button_Name",
 >           "sub_button": [
 >               {
 >                   "type": "click",
 >                   "name": "Sub_button_name",
 >                   "key": "unique_click_key_1"
 >               },
 >               {
 >                   "type": "view",
 >                   "name": "Sub_button_name",
 >                   "url": "http://view-example-url.com"
 >               },
 >               {
 >                   "type": "click",
 >                   "name": "Sub_button_name",
 >                   "key": "unique_click_key_2"
 >               }
 >           ]
 >              
 >       },
 >       {
 >           "name": "Button_Name",
 >           "sub_button": [
 >               {
 >                   "type": "click",
 >                   "name": "Sub_button_name",
 >                   "key": "unique_click_key_3"
 >               },
 >               {
 >                   "type": "view",
 >                   "name": "Sub_button_name",
 >                   "url": "http://view-example-url.com"
 >               },
 >               {
 >                   "type": "click",
 >                   "name": "Sub_button_name",
 >                   "key": "unique_click_key_4"
 >               }
 >           ]
 >              
 >       }
 >   ]
 > }