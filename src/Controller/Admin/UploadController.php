<?php

namespace App\Controller\Admin;

use \Flow as Flow;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/admin/upload', name: 'upload')]
class UploadController extends AbstractController
{
    #[Route(path: '/', name: 'upload', methods: ['GET', 'POST'])]
    public function upload(): Response
    {
        $tmpDirPath =  $this->getParameter('kernel.project_dir') . '/../tmp';
        $request = new Flow\Request();
        $uploadFileName = uniqid() . "_" . $request->getFileName();
        $uploadPath = $tmpDirPath . '/' . $uploadFileName;
        
        Flow\Basic::save(
            $uploadPath,
            new Flow\Config([
                'tempDir' => $tmpDirPath 
            ]),
            $request
        );
        
        return $this->json([
            'file' => $uploadPath
        ]);
    }
}
