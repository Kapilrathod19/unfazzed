<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('offers-for-you.destroy', $offer->id))->attribute('data--submit', 'offer'.$offer->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    @if(!$offer->trashed())
        <a class="me-2" href="{{ route('offers-for-you.create',['id' => $offer->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.offers_for_you') ]) }}"><i class="fas fa-pen text-secondary"></i></a>
        <a class="me-3 text-danger" href="{{ route('offers-for-you.destroy', $offer->id) }}" data--submit="offer{{$offer->id}}" 
            data--confirmation='true' 
            data--ajax="true"
            data-reload="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.offers_for_you') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.offers_for_you') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt"></i>
        </a>

    @endif
    @if(auth()->user()->hasAnyRole(['admin']) && $offer->trashed())
        <a href="{{ route('offers-for-you.action',['id' => $offer->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title',['form' => __('messages.offers_for_you') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.restore_form_title',['form'=>  __('messages.offers_for_you') ]) }}"
            data-message='{{ __("messages.restore_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="fas fa-redo text-primary"></i>
        </a>
        <a href="{{ route('offers-for-you.action',['id' => $offer->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.offers_for_you') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.offers_for_you') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close() }}
