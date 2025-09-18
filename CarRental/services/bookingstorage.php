<?php
  include_once("storage.php");

  class BookingStorage extends Storage
  {
    public function __construct()
    {
      parent::__construct(new JsonIO("data/bookings.json")); 
    }
  }