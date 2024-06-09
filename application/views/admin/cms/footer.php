<div id="footerPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <div class="container-fluid animatedParent animateOnce">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Nastavení patičky webu</h6>
                    </div>
                    <div class="card-body b-b">    
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="float-left">Úprava položek</label>
                                    <div class="material-switch float-left pt-1 mx-3">
                                        <input id="setSortable" name="setSortable" type="checkbox">
                                        <label for="setSortable" class="bg-primary"></label>
                                    </div> 
                                    <label>Nastavení pozice</label>
                                    <p class="my-0 font-italic s-12 switch-note"><strong>Poznámka: <span>Nyní můžete editovat položky pomocí kliku</strong></p> 
                                    <p class="my-0 font-italic s-12 switch-note" style="display:none;"><strong>Poznámka: <span>Nyní můžete položky libovolně přemísťovat tahem myší</strong></p> 
                                    <a href="javascript:;" id=add-footer-link class="btn btn-xs btn-primary mt-2"><strong><i class="icon-plus-circle"> Přidat link</i></strong></a>
                                </div>            
                            </div>
                        </div>                            
                        <div id="footer-cols" class="row mt-1">
                            <input type="hidden" id="footerId" name="id" value="<?php echo @$footer['id']; ?>">
                            <?php if (isset($footer)): ?>
                            <?php foreach ($footer['data'] as $colIndex => $col): ?>
                                <div class="col">
                                    <div id="sortable-col-<?php echo $colIndex; ?>" class="list-group grey lighten-4" style="min-height:100px;">
                                        <?php foreach (@$col as $k => $v): ?>
                                            <?php if ($k=='logo'): ?>
                                                <div class="list-group-item" data-type="<?php echo $k; ?>"><img class="logo" src="/<?php echo config_item('app')['img_folder']; ?>logo_gymit_premium.svg" alt="Gymit" width="150" /></div>
                                            <?php elseif ($k=='text'): ?>
                                                <div class="list-group-item" data-type="<?php echo $k; ?>"><textarea rows="8" class="footer-textbox form-control p-0 s-14"><?php echo @$v; ?></textarea></div>
                                            <?php elseif ($k=='links'): ?>
                                                <?php foreach ($footer['data'][$colIndex][$k] as $linkIndex => $link): ?>
                                                <div class="list-group-item footer-link" data-type="<?php echo $k; ?>" data-link="<?php echo $link['link']; ?>">
                                                    <span><?php echo $link['text']; ?></span>
                                                    <a href="javascript:;" class="float-right rm-footer-link text-danger" title="Odstranit link"><i class="icon-delete s-18"></i></a>
                                                </div>
                                                <?php endforeach; ?>                                        
                                            <?php elseif ($k=='address'): ?>
                                                <div class="list-group-item" data-type="<?php echo $k; ?>" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                                    <span id="street"><?php echo @$general_info['data']['street']; ?></span>, 
                                                    <span id="city"><?php echo @$general_info['data']['city']; ?></span><br />
                                                    <small> 
                                                        Po - Pá <span id="monday-from"><?php echo @$opening_hours['data']['monday']['from']; ?></span> - 
                                                        <span id="monday-to"><?php echo @$opening_hours['data']['monday']['to']; ?></span>, So - Ne 
                                                        <span id="saturday-from"><?php echo @$opening_hours['data']['saturday']['from']; ?></span> - 
                                                        <span id="saturday-to"><?php echo @$opening_hours['data']['saturday']['to']; ?></span>
                                                    </small>
                                                </div>  
                                            <?php elseif ($k=='social_icons'): ?>  
                                                <div class="list-group-item" data-type="<?php echo $k; ?>" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                                    <svg version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                                                        <g>
                                                            <defs><rect id="SVGID_1_" width="18" height="18" /></defs>
                                                            <path class="st0" d="M17,0H1C0.4,0,0,0.4,0,1v16c0,0.6,0.4,1,1,1h8.6v-7H7.3V8.3h2.3v-2c0-2.3,1.4-3.6,3.5-3.6c0.7,0,1.4,0,2.1,0.1v2.4h-1.4c-1.1,0-1.3,0.5-1.3,1.3v1.7h2.7L14.8,11h-2.3v7H17c0.6,0,1-0.4,1-1V1C18,0.4,17.6,0,17,0" />
                                                        </g>
                                                    </svg>
                                                    <svg class="ml-2" version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                                                        <g>
                                                            <defs><rect id="SVGID_1_" y="0" width="18" height="18" /></defs>
                                                            <path class="st0" d="M9,4.4C6.4,4.4,4.4,6.4,4.4,9c0,2.6,2.1,4.6,4.6,4.6c2.6,0,4.6-2.1,4.6-4.6C13.6,6.4,11.5,4.4,9,4.4 M9,12c-1.7,0-3-1.3-3-3c0-1.7,1.3-3,3-3s3,1.3,3,3C12,10.7,10.7,12,9,12" />
                                                            <path class="st0" d="M13.8,3.1c0.6,0,1.1,0.5,1.1,1.1c0,0.6-0.5,1.1-1.1,1.1c-0.6,0-1.1-0.5-1.1-1.1C12.7,3.6,13.2,3.1,13.8,3.1" />
                                                            <path class="st0" d="M17.5,3.1c-0.5-1.2-1.4-2.2-2.6-2.6c-0.7-0.3-1.4-0.4-2.2-0.4C11.7,0,11.4,0,9,0C6.6,0,6.2,0,5.3,0.1 c-0.7,0-1.5,0.2-2.2,0.4C1.9,0.9,0.9,1.9,0.5,3.1C0.2,3.8,0.1,4.5,0.1,5.3C0,6.3,0,6.6,0,9s0,2.8,0.1,3.7c0,0.7,0.2,1.5,0.4,2.2c0.5,1.2,1.4,2.2,2.6,2.6C3.8,17.8,4.5,18,5.3,18C6.3,18,6.6,18,9,18c2.4,0,2.8,0,3.7-0.1c0.7,0,1.5-0.2,2.2-0.4c1.2-0.5,2.2-1.4,2.6-2.6c0.3-0.7,0.4-1.4,0.4-2.2c0-1,0.1-1.3,0.1-3.7s0-2.8-0.1-3.7C17.9,4.6,17.8,3.8,17.5,3.1 M16.3,12.6c0,0.6-0.1,1.1-0.3,1.7c-0.3,0.8-0.9,1.4-1.7,1.7c-0.5,0.2-1.1,0.3-1.7,0.3c-1,0-1.2,0.1-3.7,0.1c-2.4,0-2.7,0-3.7-0.1c-0.6,0-1.1-0.1-1.7-0.3c-0.8-0.3-1.4-0.9-1.7-1.7c-0.2-0.5-0.3-1.1-0.3-1.7c0-1-0.1-1.2-0.1-3.7c0-2.4,0-2.7,0.1-3.7c0-0.6,0.1-1.1,0.3-1.7c0.3-0.8,0.9-1.4,1.7-1.7c0.5-0.2,1.1-0.3,1.7-0.3c1,0,1.2-0.1,3.7-0.1c2.4,0,2.7,0,3.7,0.1c0.6,0,1.1,0.1,1.7,0.3c0.8,0.3,1.4,0.9,1.7,1.7c0.2,0.5,0.3,1.1,0.3,1.7c0,1,0.1,1.2,0.1,3.7C16.4,11.4,16.4,11.7,16.3,12.6L16.3,12.6L16.3,12.6z" />
                                                        </g>
                                                    </svg>                                                                               
                                                </div>                                                                                 
                                            <?php else: ?>
                                                <div class="list-group-item" data-type="<?php echo $k; ?>" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                                    <span id="<?php echo $k; ?>"><?php echo @$general_info['data'][$k]; ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>                                
                            <?php endforeach; ?> 
                            <?php else: ?>
                            <div class="col">
                                <div id="sortable-col-1" class="list-group grey lighten-4" style="min-height:100px;">
                                    <div class="list-group-item" data-type="logo"><img class="logo" src="/<?php echo config_item('app')['img_folder']; ?>logo_gymit_premium.svg" alt="Gymit" width="150" /></div>
                                    <div class="list-group-item" data-type="text"><textarea rows="8" class="footer-textbox form-control p-0 s-14"></textarea></div>                                  
                                </div>
                            </div>
                            <div class="col">
                                <div id="sortable-col-2" class="list-group grey lighten-4" style="min-height:100px;">
                                <div class="list-group-item footer-link" data-type="links" data-link="">
                                        <span>Link 1</span>
                                        <a href="javascript:;" class="float-right rm-footer-link text-danger" title="Odstranit link"><i class="icon-delete s-18"></i></a>
                                    </div>
                                    <div class="list-group-item footer-link" data-type="links" data-link="">
                                        <span>Link 2</span>
                                        <a href="javascript:;" class="float-right rm-footer-link text-danger" title="Odstranit link"><i class="icon-delete s-18"></i></a>
                                    </div>  
                                </div> 
                            </div>
                            <div class="col">                                                               
                                <div id="sortable-col-3" class="list-group grey lighten-4" style="min-height:100px;">
                                    <div class="list-group-item" data-type="address" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                        <span id="street"><?php echo @$general_info['data']['street']; ?></span>, 
                                        <span id="city"><?php echo @$general_info['data']['city']; ?></span><br />
                                        <small> 
                                            Po - Pá <span id="monday-from"><?php echo @$opening_hours['data']['monday']['from']; ?></span> - 
                                            <span id="monday-to"><?php echo @$opening_hours['data']['monday']['to']; ?></span>, So - Ne 
                                            <span id="saturday-from"><?php echo @$opening_hours['data']['saturday']['from']; ?></span> - 
                                            <span id="saturday-to"><?php echo @$opening_hours['data']['saturday']['to']; ?></span>
                                        </small>
                                    </div>                                
                                    <div class="list-group-item" data-type="email" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                        <span id="email"><?php echo @$general_info['data']['email']; ?></span>
                                    </div>                                
                                    <div class="list-group-item" data-type="phone" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                        <span id="phone"><?php echo @$general_info['data']['phone']; ?></span>
                                    </div>                                
                                    <div class="list-group-item" data-type="social_icons" data-toggle="modal" data-remote="<?php echo $footerModalurl; ?>" data-target="#modal" data-modal-title="Editace nastavení pobočky" data-modal-submit="Uložit">
                                        <svg version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                                            <g>
                                                <defs><rect id="SVGID_1_" width="18" height="18" /></defs>
                                                <path class="st0" d="M17,0H1C0.4,0,0,0.4,0,1v16c0,0.6,0.4,1,1,1h8.6v-7H7.3V8.3h2.3v-2c0-2.3,1.4-3.6,3.5-3.6c0.7,0,1.4,0,2.1,0.1v2.4h-1.4c-1.1,0-1.3,0.5-1.3,1.3v1.7h2.7L14.8,11h-2.3v7H17c0.6,0,1-0.4,1-1V1C18,0.4,17.6,0,17,0" />
                                            </g>
                                        </svg>
                                        <svg class="ml-2" version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                                            <g>
                                                <defs><rect id="SVGID_1_" y="0" width="18" height="18" /></defs>
                                                <path class="st0" d="M9,4.4C6.4,4.4,4.4,6.4,4.4,9c0,2.6,2.1,4.6,4.6,4.6c2.6,0,4.6-2.1,4.6-4.6C13.6,6.4,11.5,4.4,9,4.4 M9,12c-1.7,0-3-1.3-3-3c0-1.7,1.3-3,3-3s3,1.3,3,3C12,10.7,10.7,12,9,12" />
                                                <path class="st0" d="M13.8,3.1c0.6,0,1.1,0.5,1.1,1.1c0,0.6-0.5,1.1-1.1,1.1c-0.6,0-1.1-0.5-1.1-1.1C12.7,3.6,13.2,3.1,13.8,3.1" />
                                                <path class="st0" d="M17.5,3.1c-0.5-1.2-1.4-2.2-2.6-2.6c-0.7-0.3-1.4-0.4-2.2-0.4C11.7,0,11.4,0,9,0C6.6,0,6.2,0,5.3,0.1 c-0.7,0-1.5,0.2-2.2,0.4C1.9,0.9,0.9,1.9,0.5,3.1C0.2,3.8,0.1,4.5,0.1,5.3C0,6.3,0,6.6,0,9s0,2.8,0.1,3.7c0,0.7,0.2,1.5,0.4,2.2c0.5,1.2,1.4,2.2,2.6,2.6C3.8,17.8,4.5,18,5.3,18C6.3,18,6.6,18,9,18c2.4,0,2.8,0,3.7-0.1c0.7,0,1.5-0.2,2.2-0.4c1.2-0.5,2.2-1.4,2.6-2.6c0.3-0.7,0.4-1.4,0.4-2.2c0-1,0.1-1.3,0.1-3.7s0-2.8-0.1-3.7C17.9,4.6,17.8,3.8,17.5,3.1 M16.3,12.6c0,0.6-0.1,1.1-0.3,1.7c-0.3,0.8-0.9,1.4-1.7,1.7c-0.5,0.2-1.1,0.3-1.7,0.3c-1,0-1.2,0.1-3.7,0.1c-2.4,0-2.7,0-3.7-0.1c-0.6,0-1.1-0.1-1.7-0.3c-0.8-0.3-1.4-0.9-1.7-1.7c-0.2-0.5-0.3-1.1-0.3-1.7c0-1-0.1-1.2-0.1-3.7c0-2.4,0-2.7,0.1-3.7c0-0.6,0.1-1.1,0.3-1.7c0.3-0.8,0.9-1.4,1.7-1.7c0.5-0.2,1.1-0.3,1.7-0.3c1,0,1.2-0.1,3.7-0.1c2.4,0,2.7,0,3.7,0.1c0.6,0,1.1,0.1,1.7,0.3c0.8,0.3,1.4,0.9,1.7,1.7c0.2,0.5,0.3,1.1,0.3,1.7c0,1,0.1,1.2,0.1,3.7C16.4,11.4,16.4,11.7,16.3,12.6L16.3,12.6L16.3,12.6z" />
                                            </g>
                                        </svg>                                                                               
                                    </div> 
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-primary mt-2 px-3" id="footerSubmit">Uložit</button>  
                    </div>
                </div>
            </div>
        </div>
        <!-- Popover -->
        <div id="footer-link-popover" class="d-none">
            <div style="width:300px;">
                <input type="text" class="form-control link-name" placeholder="Název odkazu" />
                <input type="text" class="form-control link-href mt-2" placeholder="Odkaz" />
                <button class="btn-save-footer-link btn btn-primary btn-block mt-2"><strong>Uložit</strong></button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header r-0 bg-primary">
                        <h5 class="modal-title text-white"></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                        <button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button>                    
                    </div>
                </div>
            </div>
        </div>            
    </div>
</div>
