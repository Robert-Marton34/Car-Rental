<?php
    include_once("storage.php");

    class Book{
        private $book_storage;
    

        public function __construct(IStorage $book_storage){
            $this->book_storage = $book_storage;
        }

        public function register($data){
            $booking = [
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'car_id' => $data['car_id'],
                'user_email' => $data['user_email']
            ];

            return $this->book_storage->add($booking);
        }

        public function isDateRangeConflicting($car_id, $start_date, $end_date) {
            $bookings = $this->book_storage->findAll();
    
            foreach ($bookings as $booking) {
                if ($booking['car_id'] === $car_id) {
                    $existing_start = strtotime($booking['start_date']);
                    $existing_end = strtotime($booking['end_date']);
                    $new_start = strtotime($start_date);
                    $new_end = strtotime($end_date);
    
                    if (($new_start >= $existing_start && $new_end <= $existing_end) || ($new_start <= $existing_start && $new_end >= $existing_end)) { 
                        return true; 
                    }
                }
            }
            return false; 
        }

        public function filterRange($start_date, $end_date){
            $bookings = $this->book_storage->findAll();
            $filtered_bookings = [];
            foreach ($bookings as $booking) {
                $existing_start = strtotime($booking['start_date']);
                $existing_end = strtotime($booking['end_date']);
                $new_start = strtotime($start_date);
                $new_end = strtotime($end_date);
                if (($new_start <= $existing_end && $new_end >= $existing_start) || ($new_start >= $existing_end && $new_end <= $existing_start)) { 
                    $filtered_bookings[] = $booking;
                }
            }
            return $filtered_bookings;
        }

        public function filterRes($user_email){
            $bookings = $this->book_storage->findAll();
            $filtered_bookings = [];
            foreach ($bookings as $booking) {
                if ($booking['user_email'] === $user_email) {
                    $filtered_bookings[] = $booking;
                }
            }
            return $filtered_bookings;
        }

        public function all(){
            $bookings = $this->book_storage->findAll();
            return $bookings;
        }
        public function deleteBooking($id) {
            $booking = $this->book_storage->findById($id);
            if ($booking) {
                $this->book_storage->delete($id);
            }
        }

    }