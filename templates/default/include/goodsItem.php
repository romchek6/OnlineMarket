<?php if(!empty($data)): ?>

<?php

$mainClass = $parameters['mainClass'] ?? 'offers__tabs_card swiper-slide';

$classPrefix = $parameters['prefix'] ?? 'offers';

?>

<a href="<?= $this->alias(['product' => $data['alias']]) ?>" class="<?= $mainClass ?>" style="color: black ; text-decoration: none" data-productContainer>
    <div class="<?= $classPrefix ?>__tabs_image">
        <img src="<?= $this->img($data['img'] ) ?>" alt="<?= $data['name'] ?>">
    </div>
    <div class="<?= $classPrefix ?>__tabs_description">
        <div class="<?= $classPrefix ?>__tabs_name">
            <span><?= $data['name'] ?></span>
            <?= $data['short_content'] ?>
            <div class="card-main-info__table">
            <?php if(!empty($data['filters'])): ?>

                    <?= $counter = 0; ?>
                    <?php foreach ($data['filters'] as $item):?>
                        <?php if(++$counter > 3) break ?>
                        <div class="card-main-info__table-row">
                            <div class="card-main-info__table-item">
                                <?= $item['name'] ?>
                            </div>

                            <div class="card-main-info__table-item">
                                <?= implode(', ' ,array_column($item['values'] , 'name')) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        <div class="<?= $classPrefix ?>__tabs_price">
            Цена:<?= !empty($data['old_price'])?'<span class="offers_old-price">'.$data['old_price'].'</span>':'' ?><span class="offers_new-price"><?= $data['price'] ?></span>
        </div>
    </div>
    <button class="<?= $classPrefix ?>__btn" data-addToCart="<?= $data['id'] ?>">купить</button>
    <?php if(!empty($parameters['icon'])):?>
        <div class="icon-offer">
            <?= $parameters['icon'] ?>
        </div>
    <?php endif; ?>
</a>
<?php endif; ?>