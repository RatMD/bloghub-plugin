<?php
    $isCreate = $this->formGetContext() == 'create';
    $pageUrl = isset($pageUrl) ? $pageUrl : null;

    if (empty($pageUrl) && !empty($this->vars['formModel'])) {
        $post = $this->vars['formModel'];

        if (!empty($post->slug)) {
            $ctrl = \Cms\Classes\Controller::getController();
            if (empty($ctrl)) {
                $ctrl = new \Cms\Classes\Controller(\Cms\Classes\Theme::getActiveTheme());
            }
            $post->setUrl('blog/post', $ctrl);
            $pageUrl = $post->url;
        }
    }
?>
<div class="form-buttons loading-indicator-container">

    <!-- Save -->
    <a
        href="javascript:;"
        class="btn btn-primary oc-icon-check save"
        data-request="onSave"
        data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
        data-request-before-update="$(this).trigger('unchange.oc.changeMonitor')"
        <?php if (!$isCreate): ?>data-request-data="redirect:0"<?php endif ?>
        data-hotkey="ctrl+s, cmd+s">
            <?= e(trans('backend::lang.form.save')) ?>
    </a>

    <?php if (!$isCreate): ?>
        <!-- Save and Close -->
        <a
            href="javascript:;"
            class="btn btn-primary oc-icon-check save"
            data-request-before-update="$(this).trigger('unchange.oc.changeMonitor')"
            data-request="onSave"
            data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>">
                <?= e(trans('backend::lang.form.save_and_close')) ?>
        </a>
    <?php endif ?>

    <!-- Cancel -->
    <a
        href="<?= Backend::url('rainlab/blog/posts') ?>"
        class="btn btn-primary oc-icon-arrow-left cancel">
            <?= e(trans('backend::lang.form.cancel')) ?>
    </a>

    <!-- Preview -->
    <a
        href="<?= URL::to($pageUrl) ?>"
        target="_blank"
        class="btn btn-primary oc-icon-crosshairs <?php if (empty($pageUrl)): ?>hide oc-hide<?php endif ?>"
        data-control="preview-button">
            <?= e(trans('rainlab.blog::lang.blog.preview')) ?>s
    </a>

    <?php if (!$isCreate): ?>
        <!-- Delete -->
        <button
            type="button"
            class="btn btn-default empty oc-icon-trash-o"
            data-request="onDelete"
            data-request-confirm="<?= e(trans('rainlab.blog::lang.post.delete_confirm')) ?>"
            data-control="delete-button"></button>
    <?php endif ?>
</div>
