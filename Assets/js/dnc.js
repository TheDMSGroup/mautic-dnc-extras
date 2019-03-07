Mautic.dncImportOnLoad = function (container, response) {
    if (!mQuery('#dncImportProgress').length) {
        Mautic.clearModeratedInterval('dncImportProgress');
    } else {
        Mautic.setModeratedInterval('dncImportProgress', 'reloadDncImportProgress', 3000);
    }
};

Mautic.reloadDncImportProgress = function() {
    if (!mQuery('#dncImportProgress').length) {
        Mautic.clearModeratedInterval('dncImportProgress');
    } else {
        // Get progress separate so there's no delay while the import batches
        Mautic.ajaxActionRequest('plugin:mauticDoNotCOntactExtras::getImportProgress', {}, function(response) {
            if (response.progress) {
                if (response.progress[0] > 0) {
                    mQuery('.imported-count').html(response.progress[0]);
                    mQuery('.progress-bar-import').attr('aria-valuenow', response.progress[0]).css('width', response.percent + '%');
                    mQuery('.progress-bar-import span.sr-only').html(response.percent + '%');
                }
            }
        });

        // Initiate import
        mQuery.ajax({
            showLoadingBar: false,
            url: window.location + '?importbatch=1',
            success: function(response) {
                Mautic.moderatedIntervalCallbackIsComplete('dncImportProgress');

                if (response.newContent) {
                    // It's done so pass to process page
                    Mautic.processPageContent(response);
                }
            }
        });
    }
};