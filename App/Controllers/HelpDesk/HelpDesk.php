<?php

namespace App\Controllers\HelpDesk;

use Core\Http\Res;
use Core\Pipes\Pipes;
use Throwable;

class HelpDesk extends HelpDeskService
{
    /**
     * Create a new support ticket
     */
    public function _createTicket(Pipes $p)
    {
        try {
            // Validate ticket data
            $validated = $this->validateTicket($p);

            // Create ticket
            $ticket = parent::saveTicket($validated, $this->user->id);

            if ($ticket) {
                return Res::send([
                    'message' => 'Ticket created successfully',
                    'ticket' => $ticket
                ]);
            }

            return Res::status(400)->error(['message' => 'Failed to create ticket']);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
        }
    }

    /**
     * Get list of user's tickets
     */
    public function _tickets(Pipes $p)
    {
        try {
            $currentUser = $this->user->id;
            // Get tickets using service method
            $tickets = parent::getUserTickets($currentUser, $p->status);

            return Res::send($tickets);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
        }
    }

    /**
     * Get a specific ticket details with responses
     */
    public function _ticketDetails()
    {
        try {
            $id = $this->route_params['id'];

            $currentUser = $this->authenticated->id;

            $ticketData = parent::getTicketWithResponses($id, $this->user->id);

            if (!$ticketData) {
                return Res::status(404)->error(['message' => 'Ticket not found']);
            }

            return Res::send($ticketData);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
        }
    }

    /**
     * Add response to a ticket
     */
    public function _respond(Pipes $p)
    {
        try {

            // Validate response data
            $validated = $this->validateResponse($p);

            // check if a userID comes with the request else use the current authenticated user
            $userID = $this->user->id;
            // Add response using service method
            $success = parent::addTicketResponse(
                ticketId: $p->id, 
                message: $validated->message, 
                userId: $userID, 
                isAdmin: false, 
                updateStatus: $p->update_status
            );

            Res::json($success);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
            return Res::status(500)->error([
                'message' => 'An error occurred while adding response',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Close a ticket
     */
    public function _close($p)
    {
        try {
            $this->validateId($p);
            $currentUser = $this->authenticated->id;

            // Verify ticket exists and belongs to user
            $ticket = $this->ticketExists(
                ticketId: $p->id,
                userId: $currentUser
            );

            if($ticket->status === CLOSED) Res::status(400)->error("Ticket Already Closed");
            $ticket->modify(['status' => CLOSED]);
             return Res::send(['message' => "Ticket Closed"]);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
        }
    }

    /**
     * Reopen a closed ticket
     */
    public function _reOpen(Pipes $p)
    {
       try {
            $this->validateId($p);
            $currentUser = $this->authenticated->id;

            // Verify ticket exists and belongs to user
            $ticket = $this->ticketExists(
                ticketId: $p->id,
                userId: $currentUser
            );

            if($ticket->status !== CLOSED) Res::status(400)->error("Ticket is not Closed");
            $ticket->modify(['status' => OPEN]);
             return Res::send(['message' => "Ticket Re-Opened"]);
        } catch (Throwable $e) {
            return Res::status(500)->throwable($e);
        }
    }
}
