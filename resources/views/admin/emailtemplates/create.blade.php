@extends('layouts.dashboard')

@section('breadcrum')
    AMC Master
@endsection

@section('content')
    <style>
	.variables
	{
	  text-align: center;
	  font-size: 13px;
	  padding: 6px 9px;
	  border: 1px solid #dbdada;
	  border-radius: 12px;
	  cursor: normal;
	  display:inline-block;
	  margin-bottom:10px;
	  margin-right:10px;
	}
	
	.msg 
	{
		margin-top: 4px;
	    margin-bottom: 4px;
	    background: #8bf1cb;
	    padding: 10px;
	    font-weight: bold;
	    font-size: 13px;
	}
	</style>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <ol class="breadcrumb fs-sm mb-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.emailtemplates.index') }}">AMC Email Templates Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Email Template</li>
            </ol>
            <h4 class="main-title mb-0">Add Email Template</h4>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12">
            <div class="row g-3">
                <form action="{{ route('admin.emailtemplates.store') }}" method="POST" class="col-12 col-md-12 col-xl-12 pt-3">
                    @csrf
                    <div class="card card-one card-product">
                        <div class="card-body p-3">
                            <div class="row px-md-4">

								<div class="col-6 my-3">
                                    <div class="pb-1">
                                        Template Name <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <input type="text" name="name" class="form-control w-100 @error('name') is-invalid @enderror" placeholder="Enter Template Name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-4 my-3">
                                    <div class="pb-1">
                                        Status <span class="required">*</span>
                                    </div>
                                    <div class="">
                                        <select name="status" class="form-select mb-3">
                                            <option value="">Choose Status</option>
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
								
								<div class="col-12 my-3">
                                    <div class="pb-1">
                                        Variables ( Manually Copy/Paste into Variable Supported Fields below )
                                    </div>
									
									<div class='variables'>[[-CurrentDate-]]</div>
									<div class='variables'>[[-CurrentTime-]]</div>
									<div class='variables'>[[-AMCName-]]</div>
									<div class='variables'>[[-AMCInvestorDetails-]]</div>
									<div class='variables'>[[-AMCBankDetails-]]</div>
									<div class='variables'>[[-TicketID-]]</div>
									<div class='variables'>[[-TicketDate-]]</div>
									<div class='variables'>[[-TicketScheme-]]</div>
									<div class='variables'>[[-TicketSymbol-]]</div>
									<div class='variables'>[[-TicketBasketSize-]]</div>
									<div class='variables'>[[-TicketBasketNumber-]]</div>
									<div class='variables'>[[-TicketTotalUnits-]]</div>
									<div class='variables'>[[-TicketTotalUnitsInWords-]]</div>
									<div class='variables'>[[-TicketUTRNumber-]]</div>
									<div class='variables'>[[-TicketTotalAmt-]]</div>
									<div class='variables'>[[-TicketTotalAmtInWords-]]</div>
                                </div>
								
								<div class="col-12 msg" style="display:none;">
                                    
                                </div>
								
								<div class="col-12 my-3">
                                    <div class="pb-1">
                                        Email Subject (Variables Supported)
                                    </div>
                                    <div class="">
                                        <input type="text" name="subject" class="form-control w-100 @error('subject') is-invalid @enderror" placeholder="Enter Subject" id="subject"  value="{{ old('subject') }}">
                                    </div>
                                </div>
								
								<div class="col-12 my-3">
                                    <div class="pb-1">
                                        Email Message (Variables Supported)
                                    </div>
                                    <div class="">
                                        <textarea name="template" class="form-control" id="summernote" style="width:100%;height:300px;">{{ old('template') }}</textarea>
                                    </div>
                                    <div class="pb-1" style="font-size:13px;"><b>Note ::</b> Variables will be parsed when they are used in '<b>Subject</b>' and '<b>Message</b>'</div>
								</div>
								
                            </div>

                            <div class="text-align-center">
                                <button type="submit" class="btn btn-primary active mb-4 px-5 text-ali">Create Email Template</button>
                            </div>
                        </div><!-- card-body -->
                    </div><!-- card -->
                </form><!-- col -->
            </div><!-- row -->
        </div><!-- col -->
    </div><!-- row -->
	<!-- CONTENT SECTION ENDS HERE -->
@endsection

@section('script')
<script>
   $(document).ready(function() {
        $('#summernote').summernote({
            height: 300,  // Set the height of the editor
            toolbar: [
                // Customize your toolbar here
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough'  /*, 'superscript', 'subscript'*/]],
                //['fontsize', ['fontsize']],
                //['color', ['color']],
                ['para', ['ul', 'ol', /*'paragraph'*/]],
				/*
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
				*/
            ]
        });
    });
	
	function putthru(title, code)
	{
		if(navigator && navigator.clipboard)
		{
			if(code == 'amcname' || code == 'currentdate' || code == 'currenttime')
			{
			   navigator.clipboard.writeText("[[--" + code + "--]]");
			   jQuery(".msg").html(title + " Copied !!!").show().fadeOut(1000);
			}
		}
	}
</script>
@endsection
