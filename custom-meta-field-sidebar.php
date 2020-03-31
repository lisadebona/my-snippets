<?php
/* This custom field will show up under 'Status & Visibility' meta box */
function custom_meta_post_visibility_box($object) {
    wp_nonce_field(basename(__FILE__), "custom_meta_post_visibility-nonce");
    $display_post = get_post_meta($object->ID, "custom_meta_post_visibility", true);
    $selected = ($display_post==1) ? 1 : 0;
    $screen = get_current_screen();
    $action = ( isset($screen->action) && $screen->action=='add' ) ? 'add':'edit';
    $is_selected = ($display_post==1) ? ' checked':'';
    ?>
    <div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <label for="meta_display_post<?php echo $val; ?>" class="components-checkbox-control__input-container">
                    <span class="inputlabel">Display post to front page.</span>
                    <input type="checkbox" id="meta_display_post1" name="custom_meta_post_visibility" class="components-checkbox-control__input cmeta_display_post meta_display_post1" value="1"<?php echo $is_selected?>>
                    <i class="chxboxstat"></i>
                </label>
            </div>
        </div>
    </div>
    <?php  
}

function add_custom_meta_box() {
    add_meta_box("display-post-meta-box", "Post Visibility", "custom_meta_post_visibility_box", "post", "side", "high", null);
}
add_action("add_meta_boxes", "add_custom_meta_box");

function save_custom_meta_post_visibility_box($post_id, $post, $update) {
    if (!isset($_POST["custom_meta_post_visibility-nonce"]) || !wp_verify_nonce($_POST["custom_meta_post_visibility-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "post";
    if($slug != $post->post_type)
        return $post_id;

    $post_visibility = "";
    if(isset($_POST["custom_meta_post_visibility"]))
    {
        $post_visibility = $_POST["custom_meta_post_visibility"];
    }   
    update_post_meta($post_id, "custom_meta_post_visibility", $post_visibility);
}
add_action("save_post", "save_custom_meta_post_visibility_box", 10, 3);

function jupload_scripts() { 
$screen = get_current_screen();
$is_post = ( isset($screen->base) && $screen->base=='post' ) ? true:false; 
if($is_post) { ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
    jQuery.noConflict();
    jQuery(document).ready(function($){
        var selectedVal = ( typeof $("#display-post-meta-box input.cmeta_display_post:checked").val() !== 'undefined' ) ? $("#display-post-meta-box input.cmeta_display_post:checked").val() : '';
        var postmetaForm = $("#display-post-meta-box .components-base-control").clone();
        $(postmetaForm).addClass('display-post-meta-box-control');
        $(postmetaForm).insertAfter(".edit-post-sidebar .edit-post-post-schedule"); /* .edit-post-post-schedule => is the <div> class wrapper of 'Publish' option */
        if(selectedVal) {
            $(".display-post-meta-box-control input.cmeta_display_post").attr("checked",true);
        } else {
            $(".display-post-meta-box-control input.cmeta_display_post").attr("checked",false);
        }
        
        $(document).on("click",".display-post-meta-box-control input.cmeta_display_post",function(){
            if(this.checked) {
                $("input.cmeta_display_post").attr("checked",true);
            } else {
                $("input.cmeta_display_post").attr("checked",false);
            }
        });
    });
    </script>
<?php
    }
}
add_action( 'admin_print_scripts', 'jupload_scripts' );

add_action( 'admin_head', 'post_visibility_head_scripts' );
function post_visibility_head_scripts(){ ?>
    <style>
        .display-post-meta-box-control {
            margin-top: 15px;
        }
        .display-post-meta-box-control label {
            display: block;
            width: 100%;
        }
        .display-post-meta-box-control .components-base-control__field label.components-checkbox-control__input-container {
            display: block;
            width: 100%;
            position: relative;
            margin: 0 0;
            padding: 0 0 0 22px;
        }
        .display-post-meta-box-control .components-base-control__field input {
            margin-right: 2px;
            position: absolute;
            top: 1px;
            left: 0;
            z-index: 5;
            background: transparent!important;
        }
        .display-post-meta-box-control .components-base-control__field .chxboxstat {
            display: block;
            width: 16px;
            height: 16px;
            position: absolute;
            top: 1px;
            left: 0;
            z-index: 3;
            border: 2px solid transparent;
            border-radius:2px;
            transition: none;
            font-style: normal;
        }
        .display-post-meta-box-control .components-base-control__field input:checked + .chxboxstat {
            background: #11a0d2;
            border-color: #11a0d2;
        }.display-post-meta-box-control .components-base-control__field input:checked + .chxboxstat:before {
            content: "\2714";
            display: inline-block;
            position: absolute;
            top: 0px;
            left: 1px;
            color: #FFF;
            font-size: 12px;
            line-height: 1;
        }
        #display-post-meta-box label.components-checkbox-control__input-container {
            width: 100%!important;
            position: relative;
            padding-left: 22px;
        }
        #display-post-meta-box .components-base-control__field input {
            visibility: visible;
            position: absolute;
            top: 1px;
            left: 0;
        }
        /* This is the actual meta box. This will do the trick. */
        .metabox-location-side #display-post-meta-box{display:none!important;}
    </style>
<?php
}
/* end of meta box custom field */


