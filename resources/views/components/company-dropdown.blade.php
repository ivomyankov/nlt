<select class="form-control" name="company_id" id="company_id">
    <option selected disabled hidden>Select Company</option>
    @foreach ($companies as $key => $company)
        <option value="{{ $company['id'] }}" >{{ $company['name']}}</option>
    @endforeach    
</select>
<input type="text" hidden class="form-control" id="company" placeholder="Enter name" name="company" >