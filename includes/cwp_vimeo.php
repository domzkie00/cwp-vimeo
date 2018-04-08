<div class="wrap" id="clients-wp-merge-wrap">
    <h1>Clients WP - Vimeo</h1>
    <br />
    <?php settings_errors() ?>
    <div class="content-wrap">
        <?php
            $cwpvimeo_settings_options = get_option('cwpvimeo_settings_options');
            $app_key    = isset($cwpvimeo_settings_options['app_key']) ? $cwpvimeo_settings_options['app_key'] : '';
            $app_secret = isset($cwpvimeo_settings_options['app_secret']) ? $cwpvimeo_settings_options['app_secret'] : '';
            $app_token  = isset($cwpvimeo_settings_options['app_token']) ? $cwpvimeo_settings_options['app_token'] : '';
            $user_id  = isset($cwpvimeo_settings_options['user_id']) ? $cwpvimeo_settings_options['user_id'] : '';
            $user_credentials  = isset($cwpvimeo_settings_options['user_credentials']) ? $cwpvimeo_settings_options['user_credentials'] : '';
        ?>
        <br />
        <form method="post" action="options.php">
            <?php settings_fields( 'cwpvimeo_settings_options' ); ?>
            <?php do_settings_sections( 'cwpvimeo_settings_options' ); ?> 
            <table class="form-table">
                <tbody>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label>Client ID</label>
                        </th>
                        <td>
                            <input type="text" name="cwpvimeo_settings_options[app_key]" size="40" width="40" value="<?= $app_key ?>">
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label>Client Secret</label>
                        </th>
                        <td>
                            <input type="text" name="cwpvimeo_settings_options[app_secret]" size="40" width="40" value="<?= $app_secret ?>">
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label>Token</label>
                        </th>
                        <td>
                           <textarea rows="1" readonly="" name="cwpvimeo_settings_options[app_token]" style="resize: none;"><?= $app_token ?></textarea>
                        </td>
                    </tr>
                    <?php if($app_token) { ?>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label>Vimeo User ID/Username</label>
                        </th>
                        <td>
                            <input type="text" name="cwpvimeo_settings_options[user_id]" size="40" width="40" value="<?= $user_id ?>">
                        </td>
                    </tr>
                        <?php if($user_id) { ?>
                        <tr class="form-field form-required term-name-wrap">
                            <th scope="row">
                                <label>Vimeo User Credentials</label>
                            </th>
                            <td>
                               <textarea rows="5" readonly="" name="cwpvimeo_settings_options[user_credentials]"><?= $user_credentials ?></textarea>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <p>
                <input type="submit" name="save_settings" class="button button-primary" value="Save">

                <?php if (!empty($app_key) && !empty($app_secret)): ?>
                <a href="<?= admin_url( 'edit.php?post_type=bt_client&page=cwp-vimeo&cwpintegration=vimeo' ); ?>" class="button button-primary">Get Access Token</a>
                <?php endif; ?>

                <?php if (!empty($app_key) && !empty($app_secret) && !empty($app_token)): ?>
                    <input type="submit" name="save_settings" class="button button-primary" value="Save Vimeo User">
                <?php endif; ?>

                <?php if (!empty($app_key) && !empty($app_secret) && !empty($app_token) && !empty($user_id)): ?>
                <a href="<?= admin_url( 'edit.php?post_type=bt_client&page=cwp-vimeo&cwpintegration=vimeo&vimeouser=set' ); ?>" class="button button-primary">Get Vimeo User Credentials</a>
                <?php endif; ?>


            </p>
        </form>
    </div>
</div>
