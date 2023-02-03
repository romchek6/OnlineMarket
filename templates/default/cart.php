<?php  ?>
    <div class="container">
        <nav class="breadcrumbs">
            <ul class="breadcrumbs__list" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a class="breadcrumbs__link" itemprop="item" href="index.html">
                        <span itemprop="name">Главная</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
                <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a class="breadcrumbs__link" itemprop="item" href="catalog-line.html#">
                        <span itemprop="name">корзина</span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
            </ul>
        </nav>
        <h1 class="page-title h1">корзина</h1>
    </div>

    <?php if(empty($this->cart['goods'])): ?>

        <section class="catalog-internal">
            <div class="container">
                <div class="catalog-internal-wrap">
                    <p>Ваша корзина пуста</p>
                </div>
            </div>
        </section>

    <?php else: ?>
        <section class="catalog-internal">
            <div class="container">
                <div class="catalog-internal-wrap">
                    <section class="catalog-section catalog-section__line">

                        <div class="basket-top">

                            <div class="total-basket-price">
                                Итого:
                                <span class="total-basket-price_old-price" data-totalOldSum><?= $this->cart['total_old_sum'] ?$this->cart['total_old_sum'] . ' .руб': '' ?></span>
                                <span class="total-basket-price_new-price" data-totalSum><?= $this->cart['total_sum']?> .руб</span>
                            </div>
                            <div class="basket-btns">
                                <button class="basket-btn">Перейти к оформлению</button>
                                <a href="<?= $this->alias(['cart' => 'remove']) ?>" class="basket-btn">Очистить корзину</a>
                            </div>
                        </div>

                        <div class="catalog-section__wrapper">
                            <div class="catalog-section-items">
                                <div class="catalog-section-items__wrapper">

                                    <?php foreach ($this->cart['goods'] as $item): ?>
                                        <div class="card-item card-item__internal card-item__line" data-productContainer>
                                            <div class="card-item__tabs_image">
                                                <img src="<?= $this->img($item['img']) ?>" alt="">
                                            </div>
                                            <div class="card-item__tabs_description">
                                                <div class="card-item__tabs_name">
                                                    <span><?= $item['name'] ?></span>
                                                </div>
                                                <div class="card-item__tabs_price">
                                                    Цена:
                                                    <?php if(!empty($item['old_price'])): ?>
                                                        <span class="card-item_old-price"><?= $item['old_price'] ?></span>
                                                    <?php endif; ?>
                                                    <span class="card-item_new-price"><?= $item['price'] ?></span>
                                                </div>
                                            </div>
                                            <a href="<?= $this->alias(['cart' => 'remove' , 'id' => $item['id']]) ?>" style="text-decoration: none" class="card-item__btn">
                                                Удалить
                                            </a>
                                            <span class="card-main-info-size__body">
                                              <span class="card-main-info-size__control card-main-info-size__control_minus js-counterDecrement" data-quantityMinus></span>
                                              <span class="card-main-info-size__count js-counterShow unselectable" data-quantity><?= $this->cart['goods'][$item['id']]['qty']?></span>
                                              <span class="card-main-info-size__control card-main-info-size__control_plus js-counterIncrement" data-quantityPlus></span>
                                            </span>

                                            <?php if($item['hot']): ?>
                                                <div class="icon-offer">
                                                    <svg>
                                                        <use xlink:href="<?= PATH . TEMPLATE ?>assets/img/icons.svg#hot"></use>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            <a style="display: none" data-addToCart="<?= $item['id'] ?>" data-toCartAdded href="#" class="card-main-info__button button-basket button-blue button-big button">

                                            </a>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        <section class="order-registration">
            <div class="container">
                <form class="order-registration-form" method="post" action="<?= $this->alias('orders') ?>">
                    <?php if(!empty($this->payments)): ?>
                        <div class="order-registration-payment">
                            <div class="order-registration-titel">Оплата</div>
                            <div class="order-registration-radio">
                                <?php foreach ($this->payments as $key => $item): ?>
                                    <label class="order-registration-radio-item">
                                        <input class="order-registration-rad-inp" type="radio" name="payments_id" value="<?= $item['id'] ?>" <?= !$key ?'checked':'' ?>>
                                        <div class="order-registration-radio-item-descr"><?= $item['name'] ?></div>
                                    </label>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($this->delivery)): ?>
                        <div class="order-registration-delivery">
                            <div class="order-registration-titel">Доставка</div>
                            <div class="order-registration-radio">
                                <?php foreach ($this->delivery as $key=>$item): ?>
                                    <label class="order-registration-radio-item">
                                        <input class="order-registration-rad-inp" type="radio" name="delivery_id" value="<?= $item['id'] ?>" <?= !$key ?'checked':'' ?>>
                                        <div class="order-registration-radio-item-descr"><?= $item['name'] ?></div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="amount-pay-wrapp">
                        Сумма к оплате:
                        <span class="amount-pay" data-totalSum><?= $this->cart['total_sum']?> .руб</span>
                    </div>
                    <input class="execute-order_btn" type="button" value="Оформить заказ" data-popup="order-popup">
                    <div class="order-popup" >
                        <div class="order-popup__inner">
                            <h2 style="text-align: center"> Оформление заказа </h2>
                            <input type="text" name="name" required placeholder="Ваше имя" value="<?= $this->setFormValues('name' , 'userData') ?>">
                            <input type="tel" name="phone" title="+7(xxx)xxx-xx-xx" required placeholder="Телефон" value="<?= $this->setFormValues('phone' , 'userData') ?>">
                            <input type="email" name="email" required placeholder="E-mail" value="<?= $this->setFormValues('email' , 'userData') ?>">
                            <textarea name="address" rows="5" placeholder="Адресс"><?= $this->setFormValues('address' , 'userData') ?></textarea>
                            <div class="amount-pay-wrapp">
                                Сумма к оплате:
                                <span class="amount-pay" data-totalSum><?= $this->cart['total_sum'] ?> .руб</span>
                            </div>
                            <div class="send-order">
                                <input class="execute-order_btn" type="submit"  value="Оформить заказ">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    <?php endif; ?>

