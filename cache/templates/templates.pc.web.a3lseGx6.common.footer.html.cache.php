
<div class="tab_selector">
    <div class="tab_selector_inner">
        <div class="tab_item t4 homeIcon " id='tab_item_t4_homeTab'>
            <div class="tab_icon_box">
                <div class="tabImgWrapper tabIcon"></div>
            </div>
            <div class="tab_hint">
                <div class="tab_hint_text homeTab" >
                    首页
                </div>
            </div>
        </div>
        <!--<div class="tab_item t4 messageIcon">-->
        <!--    <div class="tab_icon_box">-->
        <!--        <div class="tabImgWrapper tabIcon"></div>-->
        <!--    </div>-->
        <!--    <div class="tab_hint">-->
        <!--        <div class="tab_hint_text homeTab">-->
        <!--            消息-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="message_remind" style="display: none;"><span class="unread_num">0</span></div>-->
        <!--</div>-->
        <div class="tab_item t4 mineIcon" id='tab_item_t4_mineIcon'>
            <div class="tab_icon_box">
                <div class="tabImgWrapper tabIcon"></div>
            </div>
            <div class="tab_hint">
                <div class="tab_hint_text homeTab">
                    我的
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
       <script>
		$(function(){
		    $("#tab_item_t4_mineIcon").bind('click',function(){
                window.location.href='<?php echo SITE_URL; ?>?s=member';
             });
             $("#tab_item_t4_homeTab").bind('click',function(){
                window.location.href='<?php echo SITE_URL; ?>';
             });
		});
		</script>
</html>	