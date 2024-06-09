
<div id="cmsMenuPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Nastavení menu</h6>
                    </div>
                    <div class="card-body b-b">    
                        <form id="menuForm" class="col-12" data-ajax="<?php echo $saveMenuUrl; ?>">
                            <input type="hidden" name="id" value="<?php echo $menu['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $menu['name']; ?>">
                            <?php foreach ($menu['items'] as $k => $item): ?>
                            <div class="form-group row">
                                <input type="hidden" name="items[<?php echo $k; ?>][url]" value="<?php echo $item['url']; ?>" />
                                <input type="text" name="items[<?php echo $k; ?>][name]" class="form-control" value="<?php echo $item['name']; ?>" placeholder="Název položky" required>
                                <div class="material-switch float-right mt-2 ml-4">
                                    Zobrazit: <input id="hideItem<?php echo $k; ?>" name="items[<?php echo $k; ?>][show]" type="checkbox" <?php echo @$item['show']=='on' ? 'checked' : ''; ?>>
                                    <label for="hideItem<?php echo $k; ?>" class="ml-3 bg-info"></label>
                                </div>
                            </div>  
                            <?php endforeach; ?>
                            <div class="form-group row">
                                <input type="submit" class="btn btn-primary mt-2 px-3" value="Uložit" />  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>