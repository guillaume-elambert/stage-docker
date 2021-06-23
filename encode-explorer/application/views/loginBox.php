<div id="login">
    <form enctype="multipart/form-data" action="<?php print $this->makeLink(false, false, null, null, null, "",null); ?>" method="post">
    <?php
    if(GateKeeper::isLoginRequired())
    {
        $require_username = false;
        foreach(EncodeExplorer::getConfig("users") as $user){
            if($user[0] != null && strlen($user[0]) > 0){
                $require_username = true;
                break;
            }
        }
        if($require_username)
        {
        ?>
            <div>
                <label for="user_name"><?php print $this->getString("username"); ?>:</label>
                <input type="text" name="user_name" value="" id="user_name" />
            </div>
        <?php
        }
        ?>
        
        <div>
            <label for="user_pass"><?php print $this->getString("password"); ?>:</label>
            <input type="password" name="user_pass" id="user_pass" />
        </div>
        
        <div>
            <input type="submit" value="<?php print $this->getString("log_in"); ?>" class="button" />
        </div>
    </form>
</div>
    <?php
    }
    ?>