<div class="d-flex justify-content-end align-items-center">
    {{ html()->form('DELETE', route('faq.destroy', $faq->id))->attribute('data--submit', 'faq'.$faq->id)->open() }}
        <a class="me-2" href="{{ route('faq.create',['id' => $faq->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.faq') ]) }}"><i class="fas fa-pen text-secondary"></i></a>
        <a class="me-2" href="javascript:void(0)" data--submit="faq{{$faq->id}}" 
            data--confirmation='true' data-title="{{ __('messages.delete_form_title',['form' =>  __('messages.faq') ]) }}"
            title="{{ __('messages.delete_form_title',['form' =>  __('messages.faq') ]) }}"
        ><i class="fas fa-trash-alt text-danger"></i></a>
    {{ html()->form()->close() }}
</div>
