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
                    Hausverwalter Login
                </h1>
                  <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf
                    @error('username')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" role="alert">
                                <strong>{{ $message }}</strong>
                        </p>
                    @enderror
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Benutzer') }}</label>
                        <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Benutzername" required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" role="alert">
                            <strong>{{ $message }}</strong>
                        </p>
                    @enderror 
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Password') }}</label>
                        <input type="password" name="password" id="confirm-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required="">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                              <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3 text-sm">
                              <label for="remember" class="text-gray-500 dark:text-gray-300">Eingeloggt bleiben</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full text-white btn-frontpage bg-gladegreen focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Login</button>
                </form>
            </div>
        </div>
    </div>
  </div>
  @endsection
