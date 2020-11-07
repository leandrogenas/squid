<?php if(is_string($item)): ?>
    <li class="header"><?php echo e($item); ?></li>
<?php elseif(isset($item['header'])): ?>
    <li class="header"><?php echo e($item['header']); ?></li>
<?php elseif(isset($item['search']) && $item['search']): ?>
    <form action="<?php echo e($item['href']); ?>" method="<?php echo e($item['method']); ?>" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="<?php echo e($item['input_name']); ?>" class="form-control" placeholder="
            <?php echo e($item['text']); ?>

          ">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                  <i class="fas fa-search"></i>
                </button>
              </span>
        </div>
      </form>
<?php else: ?>
    <li class="<?php echo e($item['class']); ?>">
        <a href="<?php echo e($item['href']); ?>"
           <?php if(isset($item['target'])): ?> target="<?php echo e($item['target']); ?>" <?php endif; ?>
        >
            <i class="<?php echo e($item['icon'] ?? 'far fa-fw fa-circle'); ?> <?php echo e(isset($item['icon_color']) ? 'text-' . $item['icon_color'] : ''); ?>"></i>
            <span>
                <?php echo e($item['text']); ?>

            </span>

            <?php if(isset($item['label'])): ?>
                <span class="pull-right-container">
                    <span class="label label-<?php echo e($item['label_color'] ?? 'primary'); ?> pull-right"><?php echo e($item['label']); ?></span>
                </span>
            <?php elseif(isset($item['submenu'])): ?>
                <span class="pull-right-container">
                    <i class="fas fa-angle-left pull-right"></i>
                </span>
            <?php endif; ?>
        </a>
        <?php if(isset($item['submenu'])): ?>
            <ul class="<?php echo e($item['submenu_class']); ?>">
                <?php echo $__env->renderEach('adminlte::partials.menu-item', $item['submenu'], 'item'); ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\SyncWeb\vendor\jeroennoten\laravel-adminlte\src/../resources/views/partials/menu-item.blade.php ENDPATH**/ ?>