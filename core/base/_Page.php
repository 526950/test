<?php

trait _Page {
	private $item_on_page, $visible_pages, $current_page, $keyname = 'page';

	function getPages($count_item, $item_on_page, $visible_pages, $current_page) {
		$this->item_on_page = $item_on_page;
		$this->visible_pages = $visible_pages;
		$this->current_page = intval($current_page);
		if ($this->current_page == 0)
			$this->current_page = 1;
			
			// вычисление количества страниц
		$count_pages = intval($count_item / $this->item_on_page);
		
		if (($count_item % $this->item_on_page) != 0)
			$count_pages++;
		
		if ($this->current_page > $count_pages)
			$this->current_page = $count_pages;
		
		if ($this->current_page != $count_pages)
			$next = $this->current_page + 1;
		
		if ($this->current_page < 1)
			$this->current_page = 1;
		
		if ($this->current_page != 1)
			$prev = $this->current_page - 1;
			
			// вычисление диапазона видимых страниц, относительно текущей
		$half = intval($this->visible_pages / 2);
		if ($this->visible_pages % 2 == 0) {
			$lhalf = $half;
			$rhalf = $half - 1;
		} else
			$lhalf = $rhalf = $half;
		
		$start = $this->current_page - $lhalf;
		$end = $this->current_page + $rhalf;
		
		// корректировка диапазона
		

		$ldiff = $start - 1;
		if ($ldiff < 0) {
			$start = 1;
			$end += abs($ldiff);
		}
		
		$rdiff = $end - $count_pages;
		if ($rdiff > 0) {
			$end = $count_pages;
			if ($ldiff >= $rdiff)
				$start -= $rdiff;
		}
		if ($start < $end)
			$sheets = range($start, $end);
		if ($end != $count_pages)
			$block_next = $end + 1;
		if ($start != 1)
			$block_prev = $start - 1;
		
		$current_page = $this->current_page;
		
		return compact('sheets', 'block_prev', 'prev', 'current_page', 'next', 'block_next', 'count_pages', 'count_item');
	}
}