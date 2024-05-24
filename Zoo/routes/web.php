<?php

use Illuminate\Http\Request;
use App\Events\MessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ParksController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\FeedbackReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/', [PagesController::class, 'index']);
    Route::get('login', [PagesController::class, 'login']);
    Route::get('register', [PagesController::class, 'register']);
    Route::get('/unauthorized', [PagesController::class, 'unauthorized'])->name('unauthorized');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::group(['middleware' => ['checkRole:Admin']], function () {
        Route::get('/', [PagesController::class, 'index']);
        Route::resource('parks', App\Http\Controllers\ParksController::class);
        //Route::resource('animals', App\Http\Controllers\AnimalsController::class);
        Route::get('animals.index', [App\Http\Controllers\AnimalsController::class, 'index']);
        Route::post('animals.store', [App\Http\Controllers\AnimalsController::class, 'store']);
        Route::get('animals/create/{id}', [App\Http\Controllers\AnimalsController::class, 'create'])->name('animals.create');
        Route::get('animals/{id}/edit', [App\Http\Controllers\AnimalsController::class, 'edit'])->name('animals.edit');
        Route::post('animals/update/{id}', [App\Http\Controllers\AnimalsController::class, 'update']);
        Route::delete('animals/{id}', [App\Http\Controllers\AnimalsController::class, 'destroy'])->name('animals.destroy');
        Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
        Route::get('dashboard.filter', [App\Http\Controllers\PagesController::class, 'filter'])->name('dashboard.filter');
        // Route::resource('events', App\Http\Controllers\EventsController::class);
        Route::post('events.store', [App\Http\Controllers\EventsController::class, 'store']);
        Route::get('events.show/{event}', [App\Http\Controllers\EventsController::class, 'show'])->name('events.show');
        Route::get('events/create/{id}', [App\Http\Controllers\EventsController::class, 'create'])->name('events.create');
        Route::get('events/{id}/edit', [App\Http\Controllers\EventsController::class, 'edit'])->name('events.edit');
        Route::delete('events/{id}', [App\Http\Controllers\EventsController::class, 'destroy'])->name('events.destroy');
        Route::post('events/update/{id}', [App\Http\Controllers\EventsController::class, 'update']);
        Route::resource('tickets', App\Http\Controllers\TicketTypeController::class);
        Route::get('tickets.view',[App\Http\Controllers\TicketTypeController::class,'view']);
        Route::resource('categories', App\Http\Controllers\DemoCategoryController::class);
        Route::resource('pricings', App\Http\Controllers\PricingController::class);
        Route::resource('singleparktickets', App\Http\Controllers\SingleParkTicketController::class);
        Route::get('singleparktickets/create2/{id}', [App\Http\Controllers\SingleParkTicketController::class,'create2'])->name('create2');
        Route::post('singleparktickets/store2', [App\Http\Controllers\SingleParkTicketController::class,'store2'])->name('store2');
        Route::get('singleparktickets/show2/{id}', [App\Http\Controllers\SingleParkTicketController::class,'show2'])->name('show2');
        Route::get('inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory_page');
        Route::post('inventory.index/addTicketCapacity/{id}', [App\Http\Controllers\InventoryController::class, 'addTicketCapacity'])->name('addTicketCapacity');
        Route::post('inventory.index/addParkTicketCapacity/{id}', [App\Http\Controllers\InventoryController::class, 'addParkTicketCapacity'])->name('addParkTicketCapacity');
        Route::post('inventory.index/addEventCapacity/{id}', [App\Http\Controllers\InventoryController::class, 'addEventCapacity'])->name('addEventCapacity');
        // Route::get('set-active-tab', function(Request $request) {
        //     session(['active_tab' => $request->active_tab]);
        //     return response()->json(['success' => true]);
        // });
        Route::get('pages/bookings',[App\Http\Controllers\PagesController::class, 'bookings']);
        Route::get('pages/bookings',[App\Http\Controllers\PagesController::class, 'report']);
        Route::post('export_bookings_pdf',[App\Http\Controllers\BookingController::class,'export_pdf'])->name('export_bookings_pdf');
        Route::get('export_bookings_csv',[App\Http\Controllers\BookingController::class,'export_csv'])->name('export_bookings_csv');
        Route::resource('visitors', App\Http\Controllers\VisitorsController::class);
        Route::get('export_visitors_pdf',[App\Http\Controllers\VisitorsController::class,'export_pdf'])->name('export_visitors_pdf');
        Route::get('export_visitors_csv',[App\Http\Controllers\VisitorsController::class,'export_csv'])->name('export_visitors_csv');
        Route::resource('employees',App\Http\Controllers\EmployeeController::class);
        Route::resource('reports', App\Http\Controllers\ReportsController::class);
        Route::get('reports.productRankingFilter',[App\Http\Controllers\ReportsController::class, 'productRankingFilter'])->name('reports.productRankingFilter');
        Route::get('reports.productRanking', [App\Http\Controllers\ReportsController::class, 'productRanking'])->name('reports.productRanking');
        Route::get('export_productRanking_pdf', [App\Http\Controllers\ReportsController::class, 'exportPDF'])->name('export_productRanking_pdf');
        Route::get('export_productRanking_csv', [App\Http\Controllers\ReportsController::class, 'exportCSV'])->name('export_productRanking_csv');
        Route::get('reports.revenue', [App\Http\Controllers\ReportsController::class, 'revenue'])->name('reports.revenue');
        Route::get('export_revenue_pdf', [App\Http\Controllers\ReportsController::class, 'export_revenue_pdf'])->name('export_revenue_pdf');
        Route::get('export_revenue_csv', [App\Http\Controllers\ReportsController::class, 'export_revenue_csv'])->name('export_revenue_csv');
        Route::get('reports.revenueFilter', [App\Http\Controllers\ReportsController::class, 'revenueFilter'])->name('reports.revenueFilter');
        Route::get('bookings.index', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings.filter', [App\Http\Controllers\BookingController::class, 'filter'])->name('bookings.filter');
        Route::get('bookings.getBookings', [App\Http\Controllers\BookingController::class, 'getBookings'])->name('bookings.getBookings');
        Route::get('visitors.index', [App\Http\Controllers\VisitorsController::class, 'index'])->name('visitors.index');
        Route::get('visitors.filter', [App\Http\Controllers\VisitorsController::class, 'filter'])->name('visitors.filter');
        Route::get('visitors.search', [App\Http\Controllers\VisitorsController::class, 'search'])->name('visitors.search');
        Route::get('notifications.show/{type}/{id}',[App\Http\Controllers\NotificationController::class,'show'])->name('notifications.show');
        Route::get('notifications.index',[App\Http\Controllers\NotificationController::class,'index'])->name('notifications.index');
        Route::get('notifications.feedback/{id}',[App\Http\Controllers\NotificationController::class,'showFeedback'])->name('notifications.feedback');
        Route::get('notifications.refund/{id}',[App\Http\Controllers\NotificationController::class,'showRefund'])->name('notifications.refund');
        Route::get('notifications.soldOutTicket/{id}',[App\Http\Controllers\NotificationController::class,'showSoldOutTicket'])->name('notifications.soldOutTicket');
        Route::get('notifications.soldOutParkTicket/{id}',[App\Http\Controllers\NotificationController::class,'showSoldOutParkTicket'])->name('notifications.soldOutParkTicket');
        Route::get('notifications.getNotifications',[App\Http\Controllers\NotificationController::class,'getNotifications'])->name('notifications.getNotifications');
        Route::delete('notifications.deleteSelected',[App\Http\Controllers\NotificationController::class,'deleteSelected'])->name('notifications.deleteSelected');
        Route::delete('notifications.deleteAll',[App\Http\Controllers\NotificationController::class,'deleteAll'])->name('notifications.deleteAll');
        //Route::get('notifications.update', [App\Http\Controllers\NotificationController::class, 'update'])->name('notifications.update');
        Route::get('/unread-notifications-count', [App\Http\Controllers\NotificationController::class,'unreadNotificationsCount'])->name('unread.notifications.count');
        Route::get('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class,'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/mark-as-read', [App\Http\Controllers\NotificationController::class,'markAsRead'])->name('notifications.mark-as-read');
        Route::get('/refund', [App\Http\Controllers\RefundController::class, 'refund'])->name('refund.refundRequest');
        Route::get('/refundprocess', [App\Http\Controllers\RefundController::class, 'refundprocess']);
        Route::get('refund.filter', [App\Http\Controllers\RefundController::class, 'filter'])->name('refund.filter');
        Route::get('export_refund_pdf', [App\Http\Controllers\RefundController::class, 'exportPDF'])->name('export_refund_pdf');
        Route::get('export_refund_csv', [App\Http\Controllers\RefundController::class, 'exportCSV'])->name('export_refund_csv');
        Route::get('refund.export/{requestType}/{exportType}', [App\Http\Controllers\RefundController::class, 'export'])->name('refund.export');
        Route::post('/generate-pdf', [App\Http\Controllers\PDFController::class, 'generatePDF']);

        //Route::get('discounts',[App\Http\Controllers\DiscountsController::class, 'index']);
        //Route::get('discount.manage',[App\Http\Controllers\DiscountsController::class, 'manage']);
        Route::get('/discounts', [App\Http\Controllers\DiscountsController::class, 'index'])->name('discounts.index');
        Route::get('/discounts/manage', [App\Http\Controllers\DiscountsController::class, 'manage'])->name('discounts.manage');
        Route::post('/discounts', [App\Http\Controllers\DiscountsController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}', [App\Http\Controllers\DiscountsController::class, 'show'])->name('discounts.show');
        Route::get('/discounts/{discount}/edit', [App\Http\Controllers\DiscountsController::class, 'edit'])->name('discounts.edit');
        Route::delete('/discounts/{discount}', [App\Http\Controllers\DiscountsController::class, 'destroy'])->name('discounts.destroy');
        Route::put('/discounts/{discount}', [App\Http\Controllers\DiscountsController::class, 'update'])->name('discounts.update');
        Route::get('/discounts/select-type', [App\Http\Controllers\DiscountsController::class, 'selectType'])->name('discounts.select-type');
        Route::get('/feedback-reviews', [App\Http\Controllers\FeedbackReviewController::class, 'index'])->name('feedback_reviews.index');
        Route::get('/feedback.filter', [App\Http\Controllers\FeedbackReviewController::class, 'filter'])->name('feedback.filter');
        Route::get('/feedback.search', [App\Http\Controllers\FeedbackReviewController::class, 'search'])->name('feedback.search');
        Route::get('export_feedbackReview_pdf', [App\Http\Controllers\FeedbackReviewController::class, 'exportPDF'])->name('export_feedbackReview_pdf');
        Route::get('export_feedbackReview_csv', [App\Http\Controllers\FeedbackReviewController::class, 'exportCSV'])->name('export_feedbackReview_csv');
        Route::post('/feedback-reviews/visibility/{id}', [App\Http\Controllers\FeedbackReviewController::class, 'updateVisibility'])->name('feedback-reviews.updateVisibility');
        Route::get('/employeeReports.index', [App\Http\Controllers\EmployeeReportsController::class, 'index'])->name('employeeReports.index');
        Route::get('/employeeReports.show/{id}', [App\Http\Controllers\EmployeeReportsController::class, 'show'])->name('employeeReports.show');
        Route::get('/employeeReports.filter', [App\Http\Controllers\EmployeeReportsController::class, 'filter'])->name('employeeReports.filter');
        Route::get('/employeeReports.search', [App\Http\Controllers\EmployeeReportsController::class, 'search'])->name('employeeReports.search');
        Route::get('/export_employeeReports_pdf', [App\Http\Controllers\EmployeeReportsController::class, 'exportPDF'])->name('export_employeeReports_pdf');
        Route::get('/export_employeeReports_csv', [App\Http\Controllers\EmployeeReportsController::class, 'exportCSV'])->name('export_employeeReports_csv');
        Route::view('dashboard', 'inc.sidebar');
    });
});

Route::get('login',[PagesController::class, 'login']);
Route::get('register',[PagesController::class, 'register']);
Auth::routes();

