<form id="main-form" class="vg-wrap vg-element vg-ninteen-of-twenty" method="post" action="<?= PATH . $this->action ?>"
      enctype="multipart/form-data">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full vg-firm-background-color4 vg-box-shadow">
            <div class="vg-element vg-half vg-left">
                <div class="vg-element vg-padding-in-px">
                    <input type="submit" class="vg-text vg-firm-color1 vg-firm-background-color4 vg-input vg-button" value="Сохранить">
                </div>

                <?php if(!$this->noDelete && $this->data): ?>
                    <div class="vg-element vg-padding-in-px">
                        <a href="/admin/delete/goods/53" class="vg-text vg-firm-color1 vg-firm-background-color4 vg-input vg-button vg-center vg_delete">
                            <span>Удалить</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if($this->data): ?>
        <input id="tableId" type="hidden" name="<?= $this->columns['id_row']?>" value="<?= $this->data[$this->columns['id_row']] ?>">
    <?php endif; ?>

    <input type="hidden" name="table" value="<?= $this->table ?>">

    <?php
        foreach($this->blocks as $class =>$block){

            if(is_int($class)) $class = 'vg-content';

           echo '<div class="vg-wrap vg-element '. $class .'">';

           echo '<div class="vg-full vg-firm-background-color4 vg-box-shadow">';

           if($block){

               foreach($block as $row){

                    foreach ($this->templateArr as $template => $items){

                        if(in_array($row,$items)){
                            if(!@include $_SERVER['DOCUMENT_ROOT'] . $this->formTemplates . $template . '.php'){
                                throw new \core\base\exceptions\RouteException('Не найден шаблон '.
                                    $_SERVER['DOCUMENT_ROOT'] . $this->formTemplates . $template . '.php');
                            }
                            break;
                        }
                    }
               }
           }
           echo '</div>';
           echo '</div>';
        }
    ?>


    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full vg-firm-background-color4 vg-box-shadow">
            <div class="vg-element vg-half vg-left">
                <div class="vg-element vg-padding-in-px">
                    <input type="submit" class="vg-text vg-firm-color1 vg-firm-background-color4 vg-input vg-button" value="Сохранить">
                </div>
                <?php if(!$this->noDelete && $this->data): ?>
                    <div class="vg-element vg-padding-in-px">
                        <a href="/admin/delete/goods/53" class="vg-text vg-firm-color1 vg-firm-background-color4 vg-input vg-button vg-center vg_delete">
                            <span>Удалить</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</form>