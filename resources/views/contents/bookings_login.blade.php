@extends('layouts.app')

@section('content')

  <div class="bg-[url(../images/login.webp)] bg-no-repeat bg-cover bg-center bg-gray-700 bg-blend-multiply bg-opacity-60">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen">
        <a href="/home" class="flex items-center mb-6 text-2xl font-semibold text-white">
            <img class="w-8 h-8 mr-2" src="/img/logo_small.png" alt="logo">
            Ferienhaus Itelfingen    
        </a>
        <div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800">
            <div class="p-6 space-y-4 md:space-y-6 lg:space-y-8 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-center text-gray-900 md:text-2xl dark:text-white">
                    Kunden Login
                </h1>
                  <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('bookings.check') }}">
                    @csrf
                    @error('username')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" role="alert">
                            @if($errors->any())
                                <strong>{{$errors->first()}}</strong>
                            @endif
                        </p>
                    @enderror
                    <div>
                        <label for="id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Buchungs-Nummer</label>
                        <input type="text" name="id" id="id" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="00123" required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" role="alert">
                            <strong>{{ $message }}</strong>
                        </p>
                    @enderror 
                    <div>
                        <label for="plz" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Postleitzahl des Buchers</label>
                        <input type="plz" name="plz"  placeholder="6344" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <button type="submit" class="w-full text-white btn-frontpage bg-gladegreen focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Login</button>
                </form>
            </div>
        </div>
    </div>
  </div>
  @endsection
