@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between py-2">
                    <h1 class="h5 mb-0">Contact List</h1>
                    <form action="{{ route('welcome') }}" method="GET" class="d-flex align-items-center">
                        <div class="row gx-1">
                            <div class="col-md-6">
                                <select name="filter_by" id="filter_by" class="form-select">
                                    <option value="">Filter By</option>
                                    <option @if(request('filter_by') == "name") selected @endif value="name">Name</option>
                                    <option @if(request('filter_by') == "email") selected @endif value="email">Email</option>
                                    <option @if(request('filter_by') == "phone_number") selected @endif value="phone_number">Phone</option>
                                    <option @if(request('filter_by') == "gender") selected @endif value="gender">Gender</option>
                                    <option @if(request('filter_by') == "address") selected @endif value="address">Address</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="q" placeholder="Query" value="{{ request('q') }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                </div>
                            </div>
                        </div>
                     </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Address</th>
                      </tr>
                    </thead>
                    <tbody>
                        @forelse ($contacts as $index => $contact)
                            <tr>
                                <td>{{ $index + $contacts->firstItem() }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone_number }}</td>
                                <td>{{ $contact->gender }}</td>
                                <td>{{ $contact->address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Records Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
        <div class="my-3">
            {{ $contacts->links() }}
        </div>
    </div>
@endsection
