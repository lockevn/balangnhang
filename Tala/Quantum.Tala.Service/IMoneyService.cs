using System;
namespace Quantum.Tala.Service
{
    public interface IMoneyService
    {
        int AddVCoinOfVTCUser(string p_sAccount, string ItemCode, string p_sClientIP, int p_nMoneyToAdd);
        bool CheckAccountEXISTS(string p_sAccount);
        int GetBalanceOfVTCUser(string p_sUsername);
        int SubtractVCoinOfVTCUser(string p_sUsername, string ItemCode, string p_sClientIP, int p_nMoneyToSubtract, Quantum.Tala.Service.Business.OutputResultObject output);
    }
}
