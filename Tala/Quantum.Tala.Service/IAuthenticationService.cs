﻿using System;
namespace Quantum.Tala.Service
{
    public interface IAuthenticationService
    {
        Quantum.Tala.Service.Authentication.IUser Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword);
        Quantum.Tala.Service.Authentication.IUser AuthenticateQuantum(string p_sUsername, string p_sPassword);
        Quantum.Tala.Service.Authentication.IUser AuthenticateVTC(string p_sUsername, string p_sPassword);
        Quantum.Tala.Service.Authentication.IUser AuthenticateVTC_MD5HashedPassword(string p_sUsername, string p_sHashedPassword);
        Quantum.Tala.Service.DTO.login_logDTO LogLoginAction(string ip, string profilesnapshot, string username);
    }
}