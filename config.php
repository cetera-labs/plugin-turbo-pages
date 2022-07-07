 <?php
if ($this->getBo() && $this->getUser() && $this->getUser()->allowAdmin()) {
    $this->getBo()->addModule(array(
        'id' => 'turbopages',
        'position' => MENU_SERVICE,
        'name' => 'Турбо-страницы Яндекс',
        'icon' => '/cms/plugins/turbo-pages/images/icon.png',
        'iconCls' => 'x-fa fa-sitemap',
        'class' => 'Plugin.turbo-pages.Panel'        
    ));
}

$this->registerCronJob(__DIR__.'data/export.php');
