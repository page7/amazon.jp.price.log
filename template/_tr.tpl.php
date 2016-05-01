                <tr data-id="<?php echo $v['id']; ?>" class="<?php if($v['disable']) echo 'disable'; ?>">
                    <td>
                        <a href="http://www.amazon.co.jp/dp/<?php echo $v['code']; ?>" target="_blank" ><img class="thumb img-thumbnail" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+Cjxzdmcgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogPCEtLSBDcmVhdGVkIHdpdGggTWV0aG9kIERyYXcgLSBodHRwOi8vZ2l0aHViLmNvbS9kdW9waXhlbC9NZXRob2QtRHJhdy8gLS0+CiA8Zz4KICA8dGl0bGU+YmFja2dyb3VuZDwvdGl0bGU+CiAgPHJlY3QgZmlsbD0ibm9uZSIgaWQ9ImNhbnZhc19iYWNrZ3JvdW5kIiBoZWlnaHQ9IjEyIiB3aWR0aD0iMTIiIHk9Ii0xIiB4PSItMSIvPgogIDxnIGRpc3BsYXk9Im5vbmUiIG92ZXJmbG93PSJ2aXNpYmxlIiB5PSIwIiB4PSIwIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIiBpZD0iY2FudmFzR3JpZCI+CiAgIDxyZWN0IGZpbGw9InVybCgjZ3JpZHBhdHRlcm4pIiBzdHJva2Utd2lkdGg9IjAiIHk9IjAiIHg9IjAiIGhlaWdodD0iMTAwJSIgd2lkdGg9IjEwMCUiLz4KICA8L2c+CiA8L2c+CiA8Zz4KICA8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+CiA8L2c+Cjwvc3ZnPg==" style="background-image:url(/picture/<?php echo $v['id']; ?>.jpg);" /></a>
                    </td>
                    <td>
                        <a href="http://www.amazon.co.jp/dp/<?php echo $v['code']; ?>" target="_blank"><?php echo $v['title'] ?></a>
                    </td>
                    <?php \pt\tool\action::exec('list', $v); ?>
                    <td>
                        <button class="btn-refresh btn btn-default" title="Refresh"><span class="glyphicon glyphicon-refresh"></span></button>
                        <?php if ($v['disable']) { ?>
                        <button class="btn-status btn btn-default" title="Enable"><span class="glyphicon glyphicon-eye-open"></span></button>
                        <?php } else { ?>
                        <button class="btn-status btn btn-default" title="Disable"><span class="glyphicon glyphicon-eye-close"></span></button>
                        <?php } ?>
                        <button class="btn-delete btn btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
                    </td>
                </tr>