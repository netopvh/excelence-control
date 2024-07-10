<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function removeDesign($id)
    {
        $order = Order::query()->findOrFail($id);

        $order->update([
            'design_file' => null,
            'preview' => null
        ]);

        return redirect()->back()->with('success', 'Design removido com sucesso!');
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
        $file->storeAs('', $fileName, ['disk' => 'design']);
        return $fileName;
    }

    private function generatePreviewsIfNeeded($fileName)
    {
        $storagePath = storage_path('app/design/' . $fileName);
        $outputDir = pathinfo($fileName, PATHINFO_FILENAME);
        $outputPath = storage_path('app/preview/' . $outputDir);

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        if (pathinfo($fileName, PATHINFO_EXTENSION) === 'pdf') {
            return $this->generatePdfPreviews($storagePath, $outputPath, $outputDir);
        }

        return [];
    }

    private function generatePdfPreviews($storagePath, $outputPath, $outputDir)
    {
        try {
            $imagick = new Imagick();
            $imagick->readImage($storagePath);

            $previewFiles = [];
            foreach ($imagick as $index => $page) {
                $page->setResolution(300, 300);
                $page->setImageFormat('jpeg');
                $outputFile = $outputPath . "/page-" . ($index + 1) . ".jpg";
                $page->writeImage($outputFile);
                $previewFiles[] = 'preview/' . $outputDir . '/page-' . ($index + 1) . '.jpg';
            }

            $imagick->clear();
            $imagick->destroy();

            return $previewFiles;
        } catch (\Exception $e) {
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
}
