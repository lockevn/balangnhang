using System;
namespace Quantum.Tala.Service
{
    public interface IMoneyService
    {
        int GetBalanceOfVTCUser(string p_sUsername);
        int SubtractVCoinOfVTCUser(string p_sUsername, string ItemCode, string p_sClientIP, int p_nMoneyToSubtract);
    }
}
