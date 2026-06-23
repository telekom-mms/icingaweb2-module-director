<?php

namespace Icinga\Module\Director\Controllers;

use Icinga\Module\Director\DirectorObject\Automation\ImportExport;
use Icinga\Module\Director\Web\Controller\ActionController;
use Icinga\Module\Director\Web\Table\JobTable;
use Icinga\Module\Director\Web\Tabs\ImportTabs;

class JobsController extends ActionController
{
    protected $isApified = true;

    public function indexAction()
    {
        if ($this->getRequest()->isApiRequest()) {
            switch (strtolower($this->getRequest()->getMethod())) {
                case 'get':
                    $this->sendExport();
                    break;
                case 'post':
                    $this->acceptImport($this->getRequest()->getRawBody());
                    break;
                default:
                    $this->sendUnsupportedMethod();
            }

            return;
        }

        $this->addTitle($this->translate('Jobs'))
            ->setAutorefreshInterval(10)
            ->addAddLink($this->translate('Add a new Job'), 'director/job/add')
            ->tabs(new ImportTabs())->activate('jobs');

        (new JobTable($this->db()))->renderTo($this);
    }

    protected function acceptImport($raw)
    {
        (new ImportExport($this->db()))->unserializeJobs(json_decode($raw));
    }

    protected function sendExport()
    {
        $this->sendJson(
            $this->getResponse(),
            (new ImportExport($this->db()))->serializeAllJobs()
        );
    }
}
