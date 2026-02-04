<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class NotificationController extends BaseController
{
    public function markAsRead($id)
    {
        try {
            // Validate session
            if (!session()->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $user = session()->get('user');
            
            // Check if notification model exists
            if (class_exists('\App\Models\NotificationModel')) {
                $notificationModel = new \App\Models\NotificationModel();
                
                // Verify notification belongs to user
                $notification = $notificationModel->find($id);
                
                if (!$notification) {
                    return $this->response->setStatusCode(404)
                        ->setJSON(['success' => false, 'message' => 'Notification not found']);
                }
                
                if ($notification['user_id'] != $user['id']) {
                    return $this->response->setStatusCode(403)
                        ->setJSON(['success' => false, 'message' => 'Access denied']);
                }
                
                // Mark as read
                $result = $notificationModel->update($id, [
                    'is_read' => 1,
                    'read_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($result) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Notification marked as read'
                    ]);
                } else {
                    return $this->response->setStatusCode(500)
                        ->setJSON(['success' => false, 'message' => 'Failed to update notification']);
                }
            } else {
                // Notification system not implemented yet
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification system not implemented'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Notification API Error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Internal server error']);
        }
    }
}