<?php

namespace App\Http\Admin\Controllers;

use App\Http\Admin\Services\Trade as TradeService;

/**
 * @RoutePrefix("/admin/trade")
 */
class TradeController extends Controller
{

    /**
     * @Get("/search", name="admin.trade.search")
     */
    public function searchAction()
    {

    }

    /**
     * @Get("/list", name="admin.trade.list")
     */
    public function listAction()
    {
        $tradeService = new TradeService();

        $pager = $tradeService->getTrades();

        $this->view->setVar('pager', $pager);
    }

    /**
     * @Get("/{id:[0-9]+}/show", name="admin.trade.show")
     */
    public function showAction($id)
    {
        $tradeService = new TradeService();

        $trade = $tradeService->getTrade($id);
        $refunds = $tradeService->getRefunds($trade->id);
        $order = $tradeService->getOrder($trade->order_id);
        $account = $tradeService->getAccount($trade->owner_id);
        $user = $tradeService->getUser($trade->owner_id);

        $this->view->setVar('refunds', $refunds);
        $this->view->setVar('trade', $trade);
        $this->view->setVar('order', $order);
        $this->view->setVar('account', $account);
        $this->view->setVar('user', $user);
    }

    /**
     * @Get("/{id:[0-9]+}/status/history", name="admin.trade.status_history")
     */
    public function statusHistoryAction($id)
    {
        $tradeService = new TradeService();

        $statusHistory = $tradeService->getStatusHistory($id);

        $this->view->pick('trade/status_history');
        $this->view->setVar('status_history', $statusHistory);
    }

    /**
     * @Post("/{id:[0-9]+}/refund", name="admin.trade.refund")
     */
    public function refundAction($id)
    {
        $tradeService = new TradeService();

        $refund = $tradeService->refundTrade($id);

        $location = $this->url->get([
            'for' => 'admin.refund.show',
            'id' => $refund->id,
        ]);

        $content = [
            'location' => $location,
            'msg' => '申请退款成功',
        ];

        return $this->jsonSuccess($content);
    }

}
