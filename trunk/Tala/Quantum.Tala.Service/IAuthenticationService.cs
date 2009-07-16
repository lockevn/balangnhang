using System;
namespace Quantum.Tala.Service
{
    public interface IAuthenticationService
    {
        Quantum.Tala.Service.Authentication.IUser Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword);
        Quantum.Tala.Service.Authentication.IUser AuthenticateQuantum(string p_sUsername, string p_sPassword);
        Quantum.Tala.Service.Authentication.IUser AuthenticateVTC(string p_sUsername, string p_sPassword);
    }
}
