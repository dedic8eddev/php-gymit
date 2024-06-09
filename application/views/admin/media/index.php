<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-media" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-photo_library"></i>Galerie</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
            <div class="row">
                    <div id="photo-list" class="col-sm-9">
                        <div class="card r-0 shadow">
                            <div class="card-header white d-flex align-items-center">
                                <h6>Správa souborů</h6>
                                <div class="d-inline-flex ml-auto"> 
                                    <input type="text" class="form-control media-filter-input mr-1" id="js_media_options_filter_text" placeholder="Filtrovat v názvech...">                          

                                    <button type="button" class="btn btn-primary btn-sm mr-1 js-media-options-filter-btn" data-group="image"><i class="icon-image"></i></button>

                                    <button type="button" class="btn btn-primary btn-sm mr-1 js-media-options-filter-btn" data-group="video"><i class="icon-ondemand_video"></i></button>

                                    <button type="button" class="btn btn-primary btn-sm mr-1 js-media-options-filter-btn" data-group="unfilled">Nevyplněné</button>

                                    <button type="button" class="btn btn-primary btn-sm js-media-options-filter-btn" data-group="all">Vše</button>
                                </div>
                            </div>
                            <div class="card-body bg-light mediaScrollContent">
                                <div class="d-flex flex-wrap" id="js_media_list">
                                    <?php foreach($media as $mediaItem): ?>
                                    <div class="m-2 js-media-item media-item-type<?php echo ($mediaItem['type'] == 'image') ? '-image' : '-default'; ?>" id="js_item_id_<?php echo $mediaItem['id']; ?>" data-groups='["<?php echo $mediaItem['type']; ?>"<?php echo $mediaItem['unfilled'] ? ',"unfilled"' : ''; ?>]' data-name="<?php echo $mediaItem['file']; ?>" >
                                        <div class="media-item-container">
                                            
                                            <div class="media-item js-media-item-select" style="width:<?php echo $thumb_width; ?>px;height:<?php echo $thumb_height; ?>px; <?php echo $mediaItem['unfilled']==1 ? 'border:1px solid red;' : ''; ?>" data-url="<?php echo $url['ajax']['media']; ?>" data-id="<?php echo $mediaItem['id']; ?>">

                                                <?php if($mediaItem['type'] == 'image'): ?>
                                                <img src="<?php echo base_url(config_item('app')['media']['thumbs'].$mediaItem['file']); ?>" title="<?php echo $mediaItem['file']; ?>">
                                                <?php else:; ?>
                                                <h5 class="text-white"><?php echo ucfirst($mediaItem['type']); ?></h5>
                                                <?php endif; ?>

                                            </div>

                                            <div class="media-item-toolbox">
                                                <div class="media-item-toolbox__name" title="<?php echo $mediaItem['file']; ?>">
                                                    <?php echo ellipsize($mediaItem['name'],10); ?>
                                                </div>
                                                <div class="media-item-actions">
                                                    <a href="javascript:;" data-url="<?php echo $url['ajax']['media']; ?>" class="mr-2 js-media-item-edit" data-id="<?php echo $mediaItem['id']; ?>"><i class="icon icon-pen-angled2"></i></a>
                                                    <a href="javascript:;" data-url="<?php echo $url['ajax']['delete']; ?>" class="text-danger js-media-item-delete" data-id="<?php echo $mediaItem['id']; ?>"><i class="icon icon-trash"></i></a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                    <div class="m-2 hide" id="js_media_item_template">
                                        <div class="media-item-container">
                                            
                                            <div class="media-item js-media-item-select" style="šířka:<?php echo $thumb_width; ?>px;výška:<?php echo $thumb_height; ?>px;" data-url="<?php echo $url['ajax']['media']; ?>">
                                                <img src="" title="">
                                            </div>

                                            <div class="media-item-toolbox">
                                                <div class="media-item-toolbox__name" title=""></div>
                                                <div class="media-item-actions">
                                                    <a href="javascript:;" data-url="<?php echo $url['ajax']['media']; ?>" class="mr-2 js-media-item-edit"><i class="icon icon-pen-angled2"></i></a>
                                                    <a href="javascript:;" data-url="<?php echo $url['ajax']['delete']; ?>" class="text-danger js-media-item-delete"><i class="icon icon-trash"></i></a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="photo-detail" class="col-sm-3">
                        <div class="card r-0 shadow media-detail-box">
                            <form action="" id="js_media_item_form">
                                <div class="card-header white">
                                    <h6>Detail</h6>
                                </div>
                                <div class="card-body bg-light">

                                    <h6 class="text-center" id="js_media_detail_box_placeholder">Vyberte soubor</h6>

                                    <div id="js_media_detail_box_content" style="display:none;">
                                        <table class="mb-4">
                                            <tr>
                                                <td><b>Název</b></td>
                                                <td width="100" id="js_media_detail_box_name"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Velikost</b></td>
                                                <td width="100" id="js_media_detail_box_size"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Typ</b></td>
                                                <td width="100" id="js_media_detail_box_mime"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Rozměry</b></td>
                                                <td width="100" id="js_media_detail_box_dimensions"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Datum nahrání</b></td>
                                                <td width="100" id="js_media_detail_box_date_created"></td>
                                            </tr>
                                        </table>

                                        <div class="media-detail-box-edit">
                                            <div class="media-detail-box-edit__body">
                                                <div class="form-row mb-2">
                                                    <label for="">Titulek</label>
                                                    <input type="text" class="form-control" name="meta_tags[title]" placeholder="Title">
                                                </div>
                                                <div class="form-row">
                                                    <label for="">Alt popisek</label>
                                                    <input type="text" class="form-control" name="meta_tags[alt]" placeholder="Alternative description">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="hide" id="js_media_detail_box_footer">
                                        <input type="submit" value="Uložit" id="js_media_item_form_btn" data-url="<?php echo $url['ajax']['update']; ?>" class="btn btn-primary">
                                        <button data-url="<?php echo $url['ajax']['delete']; ?>" class="btn btn-danger js-media-item-delete">Smazat</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>