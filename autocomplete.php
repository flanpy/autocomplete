<?php
class autocomplete extends rcube_plugin {
		
		public $task = 'addressbook|mail';
		private $rc;
		private $contacts;
		
		function init() 
		{
			$this->require_plugin('jqueryui');
			$this->rc = rcmail::get_instance();
			$this->contacts = $this->rc->get_address_book(-1);
			
			//Register actions
			$this->register_action('plugin.autocomplete-get-x', array($this, 'autocomplete_get_addrb'));
			$this->register_action('plugin.autocomplete-get-mails-x', array($this, 'autocomplete_get_mails'));
			
			if($this->rc->task == 'addressbook' || $this->rc->task == 'mail') 
			{
				$this->init_ui();
			}
		}

		function init_ui()
		{
			if ($this->ui_initialized) {
				return;
			}

			$this->include_script('autocomplete.js');
			$this->ui_initialized = true;
			
		}
		
		
		/* Get suggestions in adressbook for a certain string in parameter
		** @param String 	value to research
		*/
		function autocomplete_get_addrb() 
		{
			if(!$this->ui_initialized)
				return null;

			$query_term =  rcube_utils::get_input_value('_term', RCUBE_INPUT_POST);

			$set = $this->contacts->search('*', $query_term, 0);
			
			$result = $this->parse_results($set, 4, 'name');

			$this->rc->output->command('plugin.callback_autocomplete_adressbook', $result);
			$this->rc->output->send();
		
		}
		
		function autocomplete_get_mails() 
		{

			if(!$this->ui_initialized)
				return null;

			$query_term = rcube_utils::get_input_value('_q', RCUBE_INPUT_POST);

			if($query_term===NULL)
				return null;

			$sort_column = rcmail_sort_column();
			$search_str =  'HEADER FROM '.rcube_imap_generic::escape($query_term);
			$result = $this->rc->storage->search('', $search_str, RCUBE_CHARSET, $sort_column);
			$result = $this->rc->storage->list_messages(null, 1, $sort_column, rcmail_sort_order());

			$length = min(4, count($result));
			
			$result_h = array();
			for($i = 0; $i < $length; ++$i) 
			{
				if(!in_array($result[$i]->get('from'), $result_h)) // Eliminate doubles
				{ 
					array_push($result_h, $result[$i]->get('from'));
				}
			}

			$this->rc->output->command('plugin.callback_autocomplete_mail', $result_h);
			$this->rc->output->send();

		}


		/* Parse results from search query in addressbook for jQueryUI autocompletion
		** @param rcube_result_set	result set from search query
		** @param int		number MAX of records to return
		** @return array
		*/
		private function parse_results($set, $nb, $label) 
		{
			if(!$this->ui_initialized)
				return null;

			$length = min($nb, $set->count);
			$records = $set->records;
			$result = array();
			for($i = 0; $i < $length; ++$i) 
			{
				array_push($result, $records[$i][$label]);
			}
			return $result;
		}
}

