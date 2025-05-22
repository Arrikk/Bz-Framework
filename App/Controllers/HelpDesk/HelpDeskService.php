<?php

namespace App\Controllers\HelpDesk;

use App\Controllers\Authenticated\Authenticated;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Core\Http\Res;
use Core\Model\Model;
use Core\Pipes\Pipes;
use Throwable;

class HelpDeskService extends Authenticated
{
    /**
     * Validate ticket creation parameters
     * 
     * @param Pipes $options Request parameters
     * @return object Validated data
     */
    public function validateTicket(Pipes $options)
    {
        return $options->pipe([
            'subject' => $options->subject()->isrequired()->isstring()->subject,
            'message' => $options->message()->isrequired()->isstring()->message,
            'priority' => $options->priority()->default('medium')->priority,
            'category' => $options->category()->default('general')->category
        ]);
    }

    /**
     * Validate ticket response parameters
     * 
     * @param Pipes $options Request parameters
     * @return object Validated data
     */
    public function validateResponse(Pipes $options)
    {
        return $options->pipe([
            'message' => $options->message()->isrequired()->isstring()->message,
            'id' => $options->id()->isrequired()->id,
            'status' => $options->update_status()->default(AWAITING_REPLY)->isenum(OPEN, RESOLVED, CLOSED, IN_PROGRESS, AWAITING_REPLY)->update_status,
            'priority' => $options->priority()->default(MEDIUM)->isenum(MEDIUM, HIGH, LOW)->priority,
        ]);
    }

    /**
     * Validate ticket update parameters
     * 
     * @param Pipes $options Request parameters
     * @return object Validated data
     */
    public function validateUpdate(Pipes $options)
    {
        return $options->pipe([
            'status' => $options->status()->default(null)->status,
            'priority' => $options->priority()->default(null)->priority,
            'category' => $options->category()->default(null)->category
        ]);
    }
    /**
     * Validate ticket id parameter
     * 
     * @param Pipes $options Request parameters
     * @return object Validated data
     */
    public function validateId(Pipes $options)
    {
        return $options->pipe([
            'id' => $options->id()->isrequired()->id,
        ]);
    }

    /**
     * Create a new ticket
     * 
     * @param object $data Validated ticket data
     * @return Ticket|bool Created ticket or false on failure
     */
    public function saveTicket($data, $user)
    {
        try {
            return Ticket::dump([
                'user_id' => $user,
                'subject' => $data->subject,
                'message' => $data->message,
                'priority' => $data->priority,
                'status' => OPEN,
                'category' => $data->category,
            ]);
        } catch (Throwable $e) {
            error_log("Error creating ticket: " . $e->getMessage());
            Res::status(500)->throwable($e);
        }
    }

    /**
     * Add response to a ticket
     * 
     * @param string $ticketId Ticket ID
     * @param string $message Response message
     * @param bool $isAdmin Whether response is from admin
     * @param string|null $updateStatus New ticket status
     * @return bool Success status
     */
    public function addTicketResponse($ticketId, $message, $userId, $isAdmin = false, $updateStatus = null)
    {
        
            // Verify ticket exists and belongs to user
            $ticket = $this->ticketExists($ticketId, $isAdmin  ? null : $userId);
            // Add response
            $response = TicketResponse::dump([
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'message' => $message,
                'role' => $isAdmin ? ADMIN : USER,
            ]);

            if ($response):
                // Update ticket status
                $newStatus = $updateStatus;
                if (!$newStatus) {
                    $newStatus = $isAdmin ? IN_PROGRESS : AWAITING_REPLY;
                }
                $ticket->modify(['status' => $newStatus]);
            endif;

            return $response;
    }

    /**
     * check if ticket exist
     * 
     * @param string $ticketId Ticket ID
     * @param string $userId User ID optional
     */
    public function ticketExists($ticketId, $userId = null): Ticket
    {  
        $ticket = Ticket::findTicketBy($ticketId, $userId);
            
        if (!$ticket) {
            Res::status(404)->error(['message' => 'Ticket not found']);
        }
        return $ticket;
    }
    /**
     * Update ticket status
     * 
     * @param string $ticketId Ticket ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateTicketStatus($ticketId, $status)
    {
        try {
            $ticket = Ticket::findOne(['id' => $ticketId]);

            if (!$ticket) {
                return false;
            }

            $ticket->status = $status;
            $ticket->updated_at = date('Y-m-d H:i:s');

            return $ticket->save();
        } catch (Throwable $e) {
            error_log("Error updating ticket status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update ticket properties
     * 
     * @param string $ticketId Ticket ID
     * @param object $data Update data
     * @return bool|Ticket Updated ticket or false on failure
     */
    public function updateTicket($ticketId, $data)
    {
        try {
            $ticket = Ticket::findOne(['_id' => $ticketId]);

            if (!$ticket) {
                return false;
            }

            // Update allowed fields
            if (isset($data->status) && $data->status) {
                $allowedStatuses = ['open', 'in_progress', 'awaiting_reply', 'resolved', 'closed', 'reopened'];
                if (in_array($data->status, $allowedStatuses)) {
                    $ticket->status = $data->status;
                }
            }

            if (isset($data->priority) && $data->priority) {
                $allowedPriorities = ['low', 'medium', 'high'];
                if (in_array($data->priority, $allowedPriorities)) {
                    $ticket->priority = $data->priority;
                }
            }

            if (isset($data->category) && $data->category) {
                $ticket->category = $data->category;
            }

            $ticket->updated_at = date('Y-m-d H:i:s');

            if ($ticket->save()) {
                return $ticket;
            }

            return false;
        } catch (Throwable $e) {
            error_log("Error updating ticket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user tickets with filtering
     * 
     * @param string $userId User ID
     * @param string|null $status Filter by status
     * @return array Tickets
     */
    public function getUserTickets($userId, $status = null)
    {
        return Ticket::find([
            'where.user_id' => $userId,
            'and.status' => $status,
            '$.order' => 'created_at DESC'
        ], "id, subject, message, status, priority, category, 
            date_format(created_at, '%b/%d/%Y') as createdDate,     
            date_format(updated_at, '%b/%d/%Y') as updatedDate");
    }

    /**
     * Get ticket details with responses
     * 
     * @param string $ticketId Ticket ID
     * @return object|false Ticket with responses or false if not found
     */
    public function getTicketWithResponses($ticketId, $user = null)
    {
        $ticket = Ticket::findTicketBy($ticketId, $user);

        if (!$ticket) Res::status(400)->error(['message' => 'Ticket not found']);

        $responses = TicketResponse::find(['ticket_id' => $ticketId]);

        $ticket->responses = $responses;

        return $ticket;
    }

}
