<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Imagick;

class OrderController extends Controller
{
    public function show($id)
    {
        $order = Order::query()->with('customer', 'orderProducts.product', 'employee')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function destroy($id)
    {
        $order = Order::query()->findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function uploadDesign(Request $request, $id)
    {
        $this->validateDesign($request);

        [$order, $orderProduct] = $this->getOrderAndOrderProduct($id, $request->get('order_product_id'));

        $fileName = $this->uploadFile($request->file('design'));

        $previewFiles = $this->generatePreviewsIfNeeded($fileName);

        $this->updateOrderProduct($orderProduct, $fileName, $previewFiles);

        return response()->json(['success' => true, 'message' => 'Upload realizado com sucesso.']);
    }

    public function removeDesign($id, $productId)
    {
        $order = Order::query()->findOrFail($id);
        $orderProduct = $order->orderProducts()->where('id', $productId)->firstOrFail();

        $this->removeFile($orderProduct->design_file);

        $this->removePreviews($orderProduct->preview);

        $orderProduct->update([
            'design_file' => null,
            'preview' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Upload removido com sucesso.']);
    }

    public function productsOrder(Request $request, $orderId)
    {
        $order = Order::query()->findOrFail($orderId);

        $orderProducts = $order->orderProducts()->with('product')->get();

        return DataTables::of($orderProducts)
            ->editColumn('in_stock', function ($orderProduct) {
                if ($orderProduct->in_stock === 'yes') {
                    return '<span class="badge bg-success">Sim</span>';
                } else if ($orderProduct->in_stock === 'no') {
                    return '<span class="badge bg-warning">Não</span>';
                } else if ($orderProduct->in_stock === 'partial') {
                    return '<span class="badge bg-info">Parcial</span>';
                } else {
                    return '-';
                }
            })
            ->setRowId(function ($orderProduct) {
                return $orderProduct->id; // Definindo o ID correto do orderProduct
            })
            ->rawColumns(['in_stock', 'supplier'])
            ->make(true);
    }

    public function updateStatusAndStep(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);
        $order->status = $request->status;
        $order->step = $request->step;
        $order->employee_id = $request->employee_id;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Informações atualizadas com sucesso',
        ]);
    }

    public function updateInfo(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);
        $order->orderProducts()->find($request->id)
            ->update([
                'supplier' => $request->supplier,
                'link' => $request->link,
                'obs' => $request->obs,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Informações atualizadas com sucesso',
        ]);
    }

    private function validateDesign(Request $request)
    {
        $request->validate([
            'design' => 'required|mimes:pdf,png,jpg,webp|max:10000',
        ], [
            'design.required' => 'O campo arquivo é obrigatório.',
            'design.mimes' => 'O tipo de arquivo enviado deve ser PDF, PNG, JPG ou WEBP.',
            'design.max' => 'O campo arquivo deve ter no máximo 10MB.',
        ]);
    }

    private function getOrderAndOrderProduct($orderId, $orderProductId)
    {
        $order = Order::query()->findOrFail($orderId);
        $orderProduct = $order->orderProducts()->where('id', $orderProductId)->firstOrFail();
        return [$order, $orderProduct];
    }

    private function uploadFile($file)
    {
        $fileName = time() . '.' . $file->extension();
        $filePath = 'design/' . $fileName;

        // $file->storeAs('', $fileName, ['disk' => 'design']);
        Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
        return $filePath;
    }

    // private function generatePreviewsIfNeeded($fileName)
    // {
    //     $storagePath = storage_path('app/design/' . $fileName);
    //     $outputDir = pathinfo($fileName, PATHINFO_FILENAME);
    //     $outputPath = storage_path('app/preview/' . $outputDir);

    //     if (!file_exists($outputPath)) {
    //         mkdir($outputPath, 0777, true);
    //     }

    //     if (pathinfo($fileName, PATHINFO_EXTENSION) === 'pdf') {
    //         return $this->generatePdfPreviews($storagePath, $outputPath, $outputDir);
    //     }

    //     return [];
    // }
    private function generatePreviewsIfNeeded($fileName)
    {
        // Caminho do arquivo no S3
        $s3FilePath = $fileName;

        // Caminho temporário local
        $tempPath = storage_path('app/temp/' . $fileName);

        // Baixar o arquivo do S3 para o caminho temporário local
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        Log::info($s3FilePath);
        Log::info($tempPath);

        // Baixar o arquivo do S3 para o caminho temporário local
        Storage::disk('s3')->get($s3FilePath, file_put_contents($tempPath, Storage::disk('s3')->get($s3FilePath)));

        $outputDir = pathinfo($fileName, PATHINFO_FILENAME);
        $outputPath = storage_path('app/temp/preview/' . $outputDir);

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        if (pathinfo($fileName, PATHINFO_EXTENSION) === 'pdf') {
            return $this->generatePdfPreviews($tempPath, $outputPath, $outputDir);
        }

        return [];
    }


    private function generatePdfPreviews($tempPath, $outputPath, $outputDir)
    {
        try {
            $imagick = new Imagick();
            $imagick->readImage($tempPath);

            $previewFiles = [];
            foreach ($imagick as $index => $page) {
                $page->setResolution(300, 300);
                $page->setImageFormat('jpeg');
                // $outputFile = $outputPath . "/page-" . ($index + 1) . ".jpg";
                $localOutputFile = $outputPath . "/page-" . ($index + 1) . ".jpg";

                // Armazene o arquivo no caminho local temporário
                $page->writeImage($localOutputFile);

                // $previewFiles[] = 'preview/' . $outputDir . '/page-' . ($index + 1) . '.jpg';
                $s3PreviewFile = 'preview/' . $outputDir . "/page-" . ($index + 1) . ".jpg";
                Storage::disk('s3')->put($s3PreviewFile, file_get_contents($localOutputFile), 'public');
                $previewFiles[] = $s3PreviewFile;

                // Delete o arquivo local temporário
                unlink($localOutputFile);
            }

            $imagick->clear();
            $imagick->destroy();

            // Delete o arquivo PDF local temporário
            unlink($tempPath);

            return $previewFiles;
        } catch (\Exception $e) {
            // Certifique-se de deletar o arquivo PDF local temporário em caso de erro
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            throw new \Exception('Erro ao converter o PDF: ' . $e->getMessage());
        }
    }

    private function updateOrderProduct($orderProduct, $fileName, $previewFiles)
    {
        $orderProduct->update([
            'design_file' => $fileName,
            'preview' => json_encode($previewFiles)
        ]);
    }

    private function removeFile($fileName)
    {
        // if (file_exists(storage_path('app/design/' . $fileName))) {
        //     unlink(storage_path('app/design/' . $fileName));
        // }
        Storage::disk('s3')->delete($fileName);
    }

    private function removePreviews($previewFiles)
    {
        // if (count($previewFiles) > 0) {
        //     foreach ($previewFiles as $previewFile) {
        //         if (file_exists(storage_path('app/preview/' . $previewFile))) {
        //             unlink(storage_path('app/preview/' . $previewFile));
        //         }
        //     }
        // }
        if (!empty($previewFiles)) {
            foreach ($previewFiles as $previewFile) {
                if (Storage::disk('s3')->exists($previewFile)) {
                    Storage::disk('s3')->delete($previewFile);
                }
            }
        }
    }
}
