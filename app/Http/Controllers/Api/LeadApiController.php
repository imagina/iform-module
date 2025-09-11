<?php

namespace Modules\Iform\Http\Controllers\Api;

use Imagina\Icore\Http\Controllers\CoreApiController;

//Model
use Modules\Iform\Models\Lead;
use Modules\Iform\Repositories\LeadRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Imedia\Entities\File;
use Modules\Imedia\Services\FileStoreService;
use Modules\Imedia\Repositories\FileRepository;
use Modules\Iform\Repositories\FormRepository;
use Illuminate\Support\Str;

class LeadApiController extends CoreApiController
{

  private FileStoreService $fileStoreService;

  public function __construct(Lead           $model, LeadRepository $modelRepository, FileStoreService $fileStoreService,
                              FileRepository $fileRepository, FormRepository $formRepository)
  {
    parent::__construct($model, $modelRepository);
    $this->fileStoreService = $fileStoreService;
    $this->fileRepository = $fileRepository;
    $this->formRepository = $formRepository;
  }

  public function create(Request $request): JsonResponse
  {
    $data = $request->input('attributes') ?? $request->all() ?? [];
    $form = $this->formRepository->getItem($data["form_id"]);
    $fields = $form->fields->where('type_id', 12);
    foreach ($fields as $field) {
      $fieldLabel = Str::snake($field->label);
      if ($request->hasFile("attributes.values.$fieldLabel")) {
        $params = ['filter' => ['field' => 'filename']];
        $folder = $this->fileRepository->getItem('filesLeads', json_decode(json_encode($params)));
        if (!isset($folder->id)) {
          $data = [
            'is_folder' => true,
            'filename' => 'filesLeads',
          ];
          $folder = $this->fileRepository->create($data);
        }
        $modelData = [
          'folder_id' => $folder->id,
        ];

        $file = $request->file("attributes.values.$fieldLabel");
        $savedModel = $this->fileStoreService->storeFromMultipart($file, [], $modelData);
        $data['medias_single']['file'] = $savedModel->id;
      }
    }
    return parent::create(
      new Request([
        'attributes' => $data
      ])
    );
  }
}
