<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

function gen_uuid()
{
    $uuid = array(
        'time_low' => 0,
        'time_mid' => 0,
        'time_hi' => 0,
        'clock_seq_hi' => 0,
        'clock_seq_low' => 0,
        'node' => array()
    );
    
    $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
    $uuid['time_mid'] = mt_rand(0, 0xffff);
    $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
    $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
    $uuid['clock_seq_low'] = mt_rand(0, 255);
    
    for ($i = 0; $i < 6; $i++) {
        $uuid['node'][$i] = mt_rand(0, 255);
    }
    
    $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
        $uuid['time_low'],
        $uuid['time_mid'],
        $uuid['time_hi'],
        $uuid['clock_seq_hi'],
        $uuid['clock_seq_low'],
        $uuid['node'][0],
        $uuid['node'][1],
        $uuid['node'][2],
        $uuid['node'][3],
        $uuid['node'][4],
        $uuid['node'][5]
    );
    
    return $uuid;
}

$username = 'nowдohesdmytany3344';
$name = 'Name reale2e';
$email = 'nowoemhлhouscry334@gmail.com';
$password = "saf4j35fhdre9j";

$token = requestGet("si/fetch_headers/", null, $username);
echo "1:\n";
print_r($token[0]);
echo "2:\n";
print_r($token[1]);
$tokenResult = "";

if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
    $tokenResult = $token[1];
}
echo "Result: " . $tokenResult;

$megaRandomHash = md5(number_format(microtime(true), 7, '', ''));
$device_id = 'android-' . substr($megaRandomHash, 16);

$phone_id = strtoupper(gen_uuid());
$waterfall_id = strtoupper(gen_uuid());
$guid = strtoupper(gen_uuid());

$igKey = 'fa558a0a4c9fd70e9c6a9a24811a59b62c6c0147ca8f93b9338f8283d6b4aa70';
$igV = '4';

$token = $tokenResult;

//sync:
$syncData = json_encode([
    "id" => $device_id,
    "experiments" => 'ig_android_ad_holdout_16m5_universe,ig_creation_growth_holdout,ig_android_business_conversion_social_context,ig_android_shopping,ig_android_ad_always_send_ad_attribution_id_universe,ig_android_direct_links,ig_android_insta_video_abr_resize,ig_android_special_brush,ig_android_stories_private_likes,ig_android_universe_video_production,ig_android_http_stack_experiment_2017,ig_android_enable_main_feed_reel_tray_preloading,ig_android_ad_cta_redesign_universe,ig_fbns_push,ig_android_live_special_codec_size_list,ig_android_direct_send_auto_retry,ig_android_preview_capture,ig_android_stories_book_universe,android_instagram_prefetch_suggestions_universe,ig_android_direct_video_autoplay_scroll,ig_android_offline_location_feed,ig_android_histogram_reporter,ig_android_exoplayer_4142,ig_android_draw_button_new_tool_animation,ig_android_disable_comment,ig_android_offline_likes_v2,ig_android_high_res_upload_2,ig_android_direct_address_links,ig_android_chaining_teaser_animation,ig_android_2fac,ig_android_react_native_lazy_modules_killswitch,ig_android_checkbox_instead_of_button_as_follow_affordance_universe,ig_android_insta_video_audio_encoder,ig_android_grid_video_icon,ig_android_image_disk_cache_max_entry_count,ig_android_mark_reel_seen_on_Swipe_forward,ig_android_disable_chroma_subsampling,ig_android_share_spinner,ig_android_reel_viewer_data_buffer_size,ig_android_video_reuse_surface,ig_android_offline_reel_feed,ig_android_offline_follows,ig_android_instavideo_periodic_notif,ig_android_snippets_haptic_feedback,ig_request_cache_layer,ig_android_promote_loading_screen_image,ig_android_unified_inbox,ig_android_stories_teach_gallery_location,ig_android_top_live_titles_universe,ig_android_insta_video_reconnect_viewers,ig_android_new_block_flow,ig_android_remove_followers_universe,ig_android_direct_link_style,ig_android_boomerang_feed_attribution,ig_android_text_background,ig_fbns_shared,ig_android_react_native_universe,ig_android_live_see_fewer_videos_like_this_universe,ig_android_snippets_profile_nux,ig_android_video_loopcount_int,ig_android_profile_photo_as_media,ig_android_direct_sqlite_universe,ig_android_camera_universe,ig_video_max_duration_qe_preuniverse,ig_android_ad_show_mai_cta_loading_state_universe,ig_android_swipe_navigation_x_angle_universe,ig_android_profile,ig_android_empty_feed_redesign,ig_android_direct_blue_tab,ig_android_video_single_surface,ig_android_enable_share_to_messenger,ig_android_mqtt_skywalker,ig_ranking_following,ig_family_bridges_holdout_universe,ig_android_stories_weblink_creation,ig_android_ad_fix_missing_viewed_time_events_universe,ig_android_insta_video_broadcaster_infra_perf,ig_android_sms_consent_in_edit_profile,ig_android_full_user_detail_endpoint,ig_android_mead,ig_android_profile_share_username,ig_android_asset_button_new_content_animation,ig_android_verified_comments_universe,ig_android_business_conversion_value_prop_v2,ig_android_http_stack_kz_debug,ig_android_sticker_tap_affordance_more_prominent,ig_android_etag_layer,ig_android_su_in_feed_unit_redesign,ig_android_keep_http_cache_on_user_switch,ig_android_retry_story_seen_state,ig_android_live_analytics,ig_android_su_activity_feed,ig_android_offline_commenting,ig_android_immersive_viewer,ig_android_video_keep_screen_on,ig_android_links_receivers,ig_android_family_bridge_discover,ig_android_save_collections,ig_android_gl_drawing_marks_after_undo_backing,ig_android_business_action,ig_save_insights,ig_android_channels_home,ig_android_mark_seen_state_on_viewed_impression,ig_android_non_square_first,ig_android_log_mediacodec_info,ig_android_react_native_universe_kill_switch,ig_android_stories_weblink_consumption,ig_android_insta_video_drawing,ig_android_facebook_twitter_profile_photos,ig_android_view_pager_paging_check_fix,ig_android_should_sample_ppr,ig_android_infinite_scrolling_launch,ig_android_swipeable_filters_blacklist,android_ig_fbns_kill_switch,ig_android_direct_video_autoplay,ig_android_share_to_whatsapp,ig_android_direct_mutually_exclusive_experiment_universe,ig_android_progressive_jpeg,ig_android_redirect_to_low_latency_universe,ig_android_stories_tray_etag_layer,ig_android_swipeablefilters_universe,ig_android_insta_video_sound_always_on,ig_android_share_profile_photo_to_feed_universe,ig_android_capture_slowmo_mode,ig_android_light_status_bar,ig_android_instavideo_remove_nux_comments,ig_android_instavideo_audio_only_mode,ig_fbns_blocked,ig_fbns_preload_default,ig_promotions_unit_in_insights_landing_page,ig_android_post_auto_retry_v7_21,ig_android_direct_typing_indicator,ig_android_direct_phone_number_links,ig_android_network_cancellation,ig_android_anrwatchdog,ig_android_search_client_matching,ig_android_user_detail_endpoint,ig_android_os_version_blocking,ig_android_add_follow_button_to_direct_thread_details,ig_android_sfplt,ig_android_show_sidecar_insights,ig_android_ad_watchbrowse_universe,ig_android_insta_video_consumption_titles,ig_android_family_bridge_bookmarks,ig_android_offline_main_feed,ig_android_fb_topsearch_sgp_fork_request,ig_android_sidecar,ig_android_memory_improve_universe,ig_android_media_favorites,ig_android_live_enhanced_end_screen_universe,ig_android_save_all,ig_android_insta_video_universe,ig_android_promote_from_profile_button,ig_android_stories_device_tilt,ig_android_universe_reel_video_production,ig_android_ontact_invite_universe,ig_android_offline_hashtag_feed,ig_android_disk_cache_has_sanity_check,ig_android_camera_cover_perf_improvement,ig_explore_netego,ig_android_samsung_app_badging,ig_android_disk_usage,ig_android_business_promotion,ig_android_live_save_to_camera_roll,ig_android_comment_tweaks_universe,ig_android_suggest_password_reset_on_oneclick_login,ig_android_disk_cache_match_journal_size_to_cache_max_count,ig_android_react_native_usertag,ig_android_offline_story_stickers,ig_android_feedvideoplayer_consistent_volume,ig_fbns_dump_ids,ig_android_feed_header_profile_ring_universe,ig_android_live_join_comment_ui_change,ig_in_feed_commenting,ig_android_direct_inbox_count,ig_android_rendering_controls,ig_android_live_stop_broadcast_on_404,ig_formats_and_feedbacks_holdout_universe,ig_video_copyright_whitelist,ig_android_insta_video_consumption_infra,ig_android_disable_comment_public_test,ig_android_offline_mode_holdout,ig_android_insta_video_consumption,ig_android_render_iframe_interval,ig_android_cache_layer_timeout,ig_android_pending_request_search_bar,ig_android_ad_rn_preload_universe,ig_android_toplive_verified_badges_universe,ig_android_discover_page_find_people_buttons,ig_android_explore_story_sfslt_universe,ig_android_search,ig_android_boomerang_entry,ig_android_fb_invite_in_followers_list,ig_android_insta_video_titles_universe,ig_android_send_direct_typing_indicator,ig_android_business_conversion_value_prop_navigate,ig_android_live_like_button_position_test,ig_video_use_sve_universe,ig_android_explore_verified_badges_stories_universe,ig_android_live_skin_smooth,ig_android_marauder_update_frequency,ig_android_family_bridge_share,ig_android_exoplayer_holdout,ig_android_offline_reel_profile,ig_android_add_to_last_post,ig_android_offline_freshness_toast_10_8,ig_android_offline_explore_10_8,ig_android_react_native_restart_after_error_universe,ig_android_video_captions_universe,ig_android_last_edits,ig_android_video_download_logging,ig_android_ad_carousel_redesign_universe,ig_android_comment_deep_linking_v1,ig_android_snippets_feed_tooltip,ig_android_async_network_tweak_universe,ig_android_use_software_layer_for_kc_drawing_universe,ig_android_react_native_ota,ig_android_following_follower_social_context'
]);

$hash = hash_hmac('sha256', $syncData, $igKey);
$sync = requestGet('qe/sync/', 'ig_sig_key_version=' . $igV . '&signed_body=' . $hash . '.' . urlencode($syncData),
    $username);
print_r($sync);

//register
/*
 *   phone_id: uuid,
            username: that.username,
            first_name: that.name,
            guid: guid,
            email: that.email,
            force_sign_up_code: "",
            qs_stamp: "",
password: that.password
 */
$data = [
    'waterfall_id' => $waterfall_id,
    'phone_id' => $phone_id,
    'email' => $email,
    'username' => $username,
    'password' => $password,
    'device_id' => $device_id,
    "guid" => $guid,
    'first_name' => $name,
    'force_sign_up_code' => '',//?
    '_csrftoken' => $tokenResult,
];
$data['qs_stamp'] = "";

$data = json_encode($data);
//17457ca87fa243a07a7c78e03085e42fd36f1e3c2bfe7217ab35b669cdca7cd2.{\"username\": \"newusername\", \"first_name\": \"Ivan\", \"waterfall_id\": \"dcd34a15bf244b9c8274d8031b06aea5\", \"_csrftoken\": \"yYmiLwiEhW8UIHVi1LmxLyS9pd7GRlZq\", \"password\": \"asdasd\", \"email\": \"newusername@gmail.com\", \"device_id\": \"CB8AF1A6-6ED0-4901-AF00-5B5AFD461E45\"}
$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;

//$result = requestGet('accounts/create_validated/', $hash, $username);
//print_r($result);

$result = requestGet('accounts/create/', $hash, $username);
print_r($result);

//if ($response->isAccountCreated()) {
//    $this->username_id = $response->getUsernameId();
//    $this->settings->set('username_id', $this->username_id);
//    preg_match('#Set-Cookie: csrftoken=([^;]+)#', $header, $match);
//    $token = $match[1];
//    $this->settings->set('token', $token);
//}

function requestGet($endpoint, $post = null, $username)
{
    $userAgent = 'Instagram 10.14.0 Android (23/6.0.1; 480dpi; 1080x1776; LENOVO/Lenovo; Lenovo P2a42; P2a42; qcom; en_US)\n\r';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://i.instagram.com/api/v1/" . $endpoint);
    //        $headers = [
    //            "X-IG-Connection-Type: WiFi\r\n",
    //            "X-IG-Capabilities: 3boBAA==\r\n",
    //        ];
    //
    //        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    $headers = [
        "X-IG-Connection-Type: WiFi",
        "X-IG-Capabilities: 3boBAA==",
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8;',
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "$username-cookies.dat");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "$username-cookies.dat");
  
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        
    ]);
    
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_PROXY, "88.198.24.108:1080");
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
    //    if ($this->proxy) {
    //        curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
    //        if ($this->proxyAuth) {
    //            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
    //        }
    //    }
    
    $resp = curl_exec($ch);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($resp, 0, $header_len);
    $body = substr($resp, $header_len);
    
    curl_close($ch);
    
    echo "REQUEST: $endpoint\n";
    if (!is_null($post)) {
        if (!is_array($post)) {
            echo "DATA: $post\n";
        }
    }
    echo "RESPONSE: $body\n\n";
    
    return [$header, json_decode($body, true)];
}